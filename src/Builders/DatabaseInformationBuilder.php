<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Base\BaseBuilder;

class DatabaseInformationBuilder extends BaseBuilder
{
    /** @var int */
    private $maxLineLength;

    /**
     * Build.
     *
     * @return string[]
     */
    public function build(): array
    {
        $this->maxLineLength = $this->config->getMaxLineLength();
        $schemaColumnNames = array_keys($this->modelBuilder->getDatabase()->getColumns($this->table));
        $tableDefinition = $this->config->getTableDefinition($this->connection, $this->table);

        // Prepare fillable column names.
        $fillableColumnNames = $tableDefinition->getFillableColumns();
        $this->filterOutColumnsNotInSchema($fillableColumnNames, $schemaColumnNames);

        // Prepare guarded column names.
        $guardedColumnNames = $tableDefinition->getGuardedColumns();
        $this->filterOutColumnsNotInSchema($guardedColumnNames, $schemaColumnNames);

        $result = [];

        // Add database connection.
        if ($this->config->getAddDatabaseConnection()) {
            $result[] = $this->indent(1) . 'protected $connection = \'' . $this->connection . '\';';
        }

        // Add database table.
        if ($this->config->getAddDatabaseTable()) {
            $result[] = $this->indent(1) . 'protected $table = \'' . $this->table . '\';';
        }

        // Add fillable column names.
        if (count($fillableColumnNames) > 0) {
            $result = array_merge($result, $this->buildFields('fillable', $fillableColumnNames));
        }

        // Add guarded column names.
        if (count($guardedColumnNames) > 0) {
            $result = array_merge($result, $this->buildFields('guarded', $guardedColumnNames));
        }

        // Footer.
        if (count($result) > 0) {
            $result = array_merge(
                [$this->indent(1) . '// Database.'],
                $result
            );
            $result[] = '';
        }

        return $result;
    }

    /**
     * Build fields.
     *
     * @param string $signature
     * @param string[] $columnNames
     * @return string[]
     */
    private function buildFields(string $signature, array $columnNames): array
    {
        // Create first line to build on.
        $lines = [$this->indent(1) . 'protected $' . $signature . ' = ['];

        $columnCounter = 1;
        foreach ($columnNames as $columnName) {
            // Get last index.
            $lastIndex = count($lines) - 1;

            // Calculate length (length of column + space + "''" +and ".").
            $columnLength = strlen($columnName) + 4;

            // Add column.
            $preparedField = '\'' . $columnName . '\'';
            if (strlen($lines[$lastIndex]) + $columnLength < $this->maxLineLength) {
                if ($columnCounter > 1) {
                    $lines[$lastIndex] .= ' ';
                }

                $lines[$lastIndex] .= $preparedField;
            } else {
                $lines[] = $this->indent(2) . $preparedField;
                $lastIndex = count($lines) - 1;
            }

            // Increase count and add ','.
            $columnCounter++;
            if ($columnCounter <= count($columnNames)) {
                $lines[$lastIndex] .= ',';
            }
        }

        // Footer.
        $lines[count($lines) - 1] .= '];';

        return $lines;
    }

    /**
     * Filter out column names not in schema.
     *
     * @param string[] $columnNames
     * @param string[] $schemaColumnNames
     */
    private function filterOutColumnsNotInSchema(array &$columnNames, array $schemaColumnNames): void
    {
        foreach ($columnNames as $index => $columnName) {
            if (!in_array($columnName, $schemaColumnNames, true)) {
                unset($columnNames[$index]);
            }
        }

        $columnNames = array_values($columnNames);
    }
}
