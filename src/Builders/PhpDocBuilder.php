<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Base\BaseBuilder;
use CoRex\Laravel\Model\Constants;
use CoRex\Laravel\Model\Helpers\Definitions\TableDefinition;
use CoRex\Laravel\Model\Interfaces\ColumnInterface;

class PhpDocBuilder extends BaseBuilder
{
    /** @var mixed[] */
    private $columnTypeMappings;

    /** @var TableDefinition */
    private $tableDefinition;

    /**
     * Build.
     *
     * @return string[]
     */
    public function build(): array
    {
        $config = $this->modelBuilder->getConfig();
        $columns = $this->modelBuilder->getDatabase()->getColumns($this->table);
        $this->columnTypeMappings = $config->getPhpDocMappings();
        $this->tableDefinition = $this->config->getTableDefinition($this->connection, $this->table);

        // Header.
        $phpdocLines = [
            '/**'
        ];

        // Phpdoc lines.
        foreach ($columns as $name => $column) {
            // Ignore Eloquent timestamp columns.
            if (in_array($name, [Constants::ELOQUENT_CREATED_AT, Constants::ELOQUENT_UPDATED_AT], true)) {
                continue;
            }

            $phpdocLines[] = $this->createPhpdocLine($column);
        }

        // Footer.
        $phpdocLines[] = ' */';

        return $phpdocLines;
    }

    /**
     * Create phpdoc line.
     *
     * @param ColumnInterface $column
     * @return string
     */
    protected function createPhpdocLine(ColumnInterface $column): string
    {
        $replace = [
            // Phpdoc type.
            'phpdocType' => 'property',

            // Handle type.
            'type' => $this->convertColumnType($column->getType()),

            // Handle name.
            'name' => $column->getName(),

            // Handle comment.
            'comment' => (string)$column->getComment()
        ];

        // Handle readonly column.
        if (in_array($column->getName(), $this->tableDefinition->getReadonlyColumns(), true)) {
            $replace['phpdocType'] = 'property-read';
        }

        // Prepare PhpDoc line.
        $phpdocLine = ' * @{phpdocType} {type} ${name} {comment}';
        foreach ($replace as $token => $value) {
            $phpdocLine = str_replace('{' . $token . '}', $value, $phpdocLine);
        }

        return rtrim($phpdocLine);
    }

    /**
     * Convert column type.
     *
     * @param string $columnType
     * @return string|null
     */
    protected function convertColumnType(string $columnType): ?string
    {
        if (array_key_exists($columnType, $this->columnTypeMappings)) {
            $columnType = $this->columnTypeMappings[$columnType];
        }

        return $columnType;
    }
}
