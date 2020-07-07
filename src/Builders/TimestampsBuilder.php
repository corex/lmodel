<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Base\BaseBuilder;
use CoRex\Laravel\Model\Constants;

class TimestampsBuilder extends BaseBuilder
{
    /**
     * Build.
     *
     * @return string[]
     */
    public function build(): array
    {
        $columnNames = array_keys($this->modelBuilder->getDatabase()->getColumns($this->table));
        $tableDefinition = $this->config->getTableDefinition($this->connection, $this->table);

        // Get timestamp details from config.
        $columnCreatedAt = $tableDefinition->getTimestampsCreatedAt();
        $columnUpdatedAt = $tableDefinition->getTimestampsUpdatedAt();
        $dateFormat = $tableDefinition->getTimestampsDateFormat();

        // Check if timestamps exists as columns.
        $timestamps = in_array($columnCreatedAt, $columnNames, true) && in_array($columnUpdatedAt, $columnNames, true);

        $result = [];

        // Constant for "created_at".
        if ($timestamps && $columnCreatedAt !== null && $columnCreatedAt !== Constants::ELOQUENT_CREATED_AT) {
            $result[] = $this->indent(1) . 'const CREATED_AT = \'' . $columnCreatedAt . '\';';
        }

        // Constant for "updated_at".
        if ($timestamps && $columnUpdatedAt !== null && $columnUpdatedAt !== Constants::ELOQUENT_UPDATED_AT) {
            $result[] = $this->indent(1) . 'const UPDATED_AT = \'' . $columnUpdatedAt . '\';';
        }

        // Date format.
        if ($timestamps && $dateFormat !== null) {
            $result[] = $this->indent(1) . 'protected $dateFormat = \'' . $dateFormat . '\';';
        }

        // Set timestamps false if not found.
        if (!$timestamps) {
            $result[] = $this->indent(1) . 'public $timestamps = false;';
        }

        // Add header and footer.
        if (count($result) > 0) {
            $result = array_merge([$this->indent(1) . '// Timestamps.'], $result);
            $result = array_merge($result, ['']);
        }

        return $result;
    }
}
