<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Base\BaseBuilder;
use CoRex\Laravel\Model\Constants;
use CoRex\Laravel\Model\Interfaces\ColumnInterface;

class PhpDocBuilder extends BaseBuilder
{
    /** @var mixed[] */
    private $columnTypeMappings;

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
        $phpdocLine = ' * @{phpdocType} {type} ${name} {comment}';

        // Phpdoc type.
        $phpdocLine = str_replace('{phpdocType}', 'property', $phpdocLine);

        // Handle type.
        $columnType = $this->convertColumnType($column->getType());
        $phpdocLine = str_replace('{type}', $columnType, $phpdocLine);

        // Handle name.
        $phpdocLine = str_replace('{name}', $column->getName(), $phpdocLine);

        // Handle comment.
        $phpdocLine = str_replace('{comment}', (string)$column->getComment(), $phpdocLine);

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
