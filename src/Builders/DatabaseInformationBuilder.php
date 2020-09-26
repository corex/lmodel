<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Base\BaseBuilder;
use CoRex\Laravel\Model\Constants;

class DatabaseInformationBuilder extends BaseBuilder
{
    /**
     * Build.
     *
     * @return string[]
     */
    public function build(): array
    {
        $schemaColumnNames = array_keys($this->modelBuilder->getDatabase()->getColumns($this->table));
        $tableDefinition = $this->config->getTableDefinition($this->connection, $this->table);

        // Prepare fillable column names.
        $fillableColumnNames = $tableDefinition->getFillableColumns();
        $this->filterOutColumnsNotInSchema($fillableColumnNames, $schemaColumnNames);

        // Prepare guarded column names.
        $guardedColumnNames = $tableDefinition->getGuardedColumns();
        $this->filterOutColumnsNotInSchema($guardedColumnNames, $schemaColumnNames);

        // Prepare hidden column names.
        $hiddenAttributes = $tableDefinition->getHiddenAttributes();

        // Prepare casts.
        $castAttributes = $tableDefinition->getCastAttributes();

        // Prepare appends.
        $accessors = $tableDefinition->getAccessors();

        $result = [];

        // Add database connection if table is not connected to a package.
        if ($this->packageDefinition === null && $this->config->getAddDatabaseConnection()) {
            $result[] = $this->indent(1) . 'protected $connection = \'' . $this->connection . '\';';
        }

        // Add database table.
        if ($this->config->getAddDatabaseTable()) {
            $result[] = $this->indent(1) . 'protected $table = \'' . $this->table . '\';';
        }

        // Add fillable column names.
        if (count($fillableColumnNames) > 0) {
            $result[] = '';
            $comment = Constants::ATTRIBUTES_FILLABLE;
            $result = array_merge($result, $this->buildFields($comment, 'fillable', $fillableColumnNames, false));
        }

        // Add guarded column names.
        if (count($guardedColumnNames) > 0) {
            $result[] = '';
            $comment = Constants::ATTRIBUTES_GUARDED;
            $result = array_merge($result, $this->buildFields($comment, 'guarded', $guardedColumnNames, false));
        }

        // Add hidden attributes.
        if (count($hiddenAttributes) > 0) {
            $result[] = '';
            $comment = Constants::ATTRIBUTES_HIDDEN;
            $result = array_merge($result, $this->buildFields($comment, 'hidden', $hiddenAttributes, false));
        }

        // Add cast attributes.
        if (count($castAttributes) > 0) {
            $result[] = '';
            $comment = Constants::ATTRIBUTES_CASTS;
            $result = array_merge($result, $this->buildFields($comment, 'casts', $castAttributes, true));
        }

        // Add accessors (appends).
        if (count($accessors) > 0) {
            $result[] = '';
            $comment = Constants::ATTRIBUTES_ACCESSORS;
            $result = array_merge($result, $this->buildFields($comment, 'appends', $accessors, false));
        }

        // Footer.
        if (count($result) > 0) {
            $result[] = '';
        }

        return $result;
    }

    /**
     * Build fields.
     *
     * @param string $comment
     * @param string $signature
     * @param string[] $columnNames
     * @param bool $useIndex
     * @return string[]
     */
    protected function buildFields(string $comment, string $signature, array $columnNames, bool $useIndex): array
    {
        // Create first line to build on.
        $lines = [
            $this->indent(1) . '// ' . $comment,
            $this->indent(1) . 'protected $' . $signature . ' = ['
        ];

        $columnCounter = 1;
        foreach ($columnNames as $index => $columnName) {
            $line = $this->indent(2);
            if ($useIndex) {
                $line .= '\'' . $index . '\' => ';
            }
            $lines[] = $line . '\'' . $columnName . '\'';

            // Increase count and add ','.
            $columnCounter++;
            if ($columnCounter <= count($columnNames)) {
                $lines[count($lines) - 1] .= ',';
            }
        }

        // Footer.
        $lines[] = $this->indent(1) . '];';

        return $lines;
    }

    /**
     * Filter out column names not in schema.
     *
     * @param string[] $columnNames
     * @param string[] $schemaColumnNames
     */
    protected function filterOutColumnsNotInSchema(array &$columnNames, array $schemaColumnNames): void
    {
        foreach ($columnNames as $index => $columnName) {
            if (!in_array($columnName, $schemaColumnNames, true)) {
                unset($columnNames[$index]);
            }
        }

        $columnNames = array_values($columnNames);
    }
}
