<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Interfaces;

use CoRex\Laravel\Model\Exceptions\BuilderException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

interface DatabaseInterface
{
    /**
     * Set connection.
     *
     * @param string $connection
     */
    public function setConnection(string $connection): void;

    /**
     * Register Doctrine type mapping.
     *
     * @param string $databaseType
     * @param string $doctrineType
     */
    public function registerDoctrineTypeMapping(string $databaseType, string $doctrineType): void;

    /**
     * Get tables.
     *
     * @return string[]
     */
    public function getTables(): array;

    /**
     * Has table.
     *
     * @param string $table
     * @return bool
     */
    public function hasTable(string $table): bool;

    /**
     * Get columns.
     *
     * @param string $table
     * @return ColumnInterface[]
     */
    public function getColumns(string $table): array;

    /**
     * Get rows by name and column.
     *
     * @param string $table
     * @param string $nameColumn
     * @param string $valueColumn
     * @return mixed[]
     * @throws BuilderException
     */
    public function getRows(string $table, string $nameColumn, string $valueColumn): array;

    /**
     * Schema manager.
     *
     * @return AbstractSchemaManager
     */
    public function getSchemaManager(): AbstractSchemaManager;

    /**
     * Database platform.
     *
     * @return AbstractPlatform
     */
    public function getDatabasePlatform(): AbstractPlatform;
}
