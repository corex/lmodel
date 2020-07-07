<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Helpers;

use CoRex\Laravel\Model\Helpers\Database\Column;
use CoRex\Laravel\Model\Interfaces\ColumnInterface;
use CoRex\Laravel\Model\Interfaces\DatabaseInterface;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\Facades\DB;

class Database implements DatabaseInterface
{
    /** @var string */
    private $connection;

    /** @var AbstractSchemaManager */
    private $schemaManager;

    /**
     * Set connection.
     *
     * @param string $connection
     */
    public function setConnection(string $connection): void
    {
        $this->connection = $connection;
    }

    /**
     * Register Doctrine type mapping.
     *
     * @param string $databaseType
     * @param string $doctrineType
     * @throws DBALException
     */
    public function registerDoctrineTypeMapping(string $databaseType, string $doctrineType): void
    {
        $this->getDatabasePlatform()->registerDoctrineTypeMapping($databaseType, $doctrineType);
    }

    /**
     * Get tables.
     *
     * @return string[]
     */
    public function getTables(): array
    {
        return $this->getSchemaManager()->listTableNames();
    }

    /**
     * Has table.
     *
     * @param string $table
     * @return bool
     */
    public function hasTable(string $table): bool
    {
        return $this->getSchemaManager()->tablesExist([$table]);
    }

    /**
     * Get columns.
     *
     * @param string $table
     * @return ColumnInterface[]
     */
    public function getColumns(string $table): array
    {
        $doctrineColumns = $this->getSchemaManager()->listTableDetails($table)->getColumns();

        $columns = [];
        foreach ($doctrineColumns as $name => $column) {
            $columns[$name] = new Column(
                [
                    'name' => $column->getName(),
                    'type' => $column->getType()->getName(),
                    'comment' => $column->getComment(),
                ]
            );
        }

        return $columns;
    }

    /**
     * Get rows by name and column.
     *
     * @param string $table
     * @param string $nameColumn
     * @param string $valueColumn
     * @return mixed[]
     */
    public function getRows(string $table, string $nameColumn, string $valueColumn): array
    {
        if (!$this->hasTable($table)) {
            return [];
        }

        $query = DB::connection($this->connection)->table($table);
        $query->orderBy($nameColumn);
        $result = $query->pluck($valueColumn, $nameColumn)->all();

        return $result;
    }

    /**
     * Get schema manager.
     *
     * @return AbstractSchemaManager
     */
    public function getSchemaManager(): AbstractSchemaManager
    {
        if ($this->schemaManager === null) {
            $this->schemaManager = DB::connection($this->connection)->getDoctrineSchemaManager();
        }

        return $this->schemaManager;
    }

    /**
     * Get database platform.
     *
     * @return AbstractPlatform
     */
    public function getDatabasePlatform(): AbstractPlatform
    {
        return $this->getSchemaManager()->getDatabasePlatform();
    }
}
