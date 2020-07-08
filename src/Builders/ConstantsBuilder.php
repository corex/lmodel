<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Base\BaseBuilder;
use CoRex\Laravel\Model\Constants;
use CoRex\Laravel\Model\Exceptions\BuilderException;
use CoRex\Laravel\Model\Helpers\Definitions\ConstantDefinition;

class ConstantsBuilder extends BaseBuilder
{
    /**
     * Build.
     *
     * @return string[]
     * @throws BuilderException
     */
    public function build(): array
    {
        // Get definitions.
        $tableDefinition = $this->config->getTableDefinition($this->connection, $this->table);
        $definitions = $tableDefinition->getConstantDefinitions();
        if (count($definitions) === 0) {
            return [];
        }

        $result = [];
        foreach ($definitions as $definition) {
            $result = array_merge($result, $this->buildConstants($definition));
        }

        return $result;
    }

    /**
     * @param ConstantDefinition $definition
     * @return string[]
     * @throws BuilderException
     */
    protected function buildConstants(ConstantDefinition $definition): array
    {
        $title = $definition->getTitle();
        $nameColumn = $definition->getNameColumn();
        $valueColumn = $definition->getValueColumn();
        $prefix = $definition->getNamePrefix();
        $suffix = $definition->getNameSuffix();
        $replace = $definition->getNameReplace();

        // Check for minimum requirements for building constants.
        if ($nameColumn === null || $valueColumn === null) {
            return [];
        }

        // Get data.
        $rows = $this->modelBuilder->getDatabase()->getRows($this->table, $nameColumn, $valueColumn);

        // Determine if value is string.
        $rowsHasStrings = $this->ifStringInRows($rows);

        // Header.
        $result = [$this->indent(1) . '// ' . $title];

        // Build constants.
        foreach ($rows as $name => $value) {
            // Prepare constant.
            $constant = mb_strtoupper($name);
            $constant = $this->replaceCharacters($constant, $replace);
            $constant = $prefix . $constant . $suffix;

            // Prepare value.
            if ($rowsHasStrings) {
                $value = '\'' . $value . '\'';
            }

            $result[] = $this->indent(1) . 'public const ' . $constant . ' = ' . $value . ';';
        }

        // Footer.
        $result[] = '';

        return $result;
    }

    /**
     * Determine if rows contains any strings.
     *
     * @param object[] $rows
     * @return bool
     */
    protected function ifStringInRows(array $rows): bool
    {
        $stringInRows = false;
        if (count($rows) === 0) {
            return $stringInRows;
        }

        foreach ($rows as $value) {
            if (!is_numeric($value)) {
                $stringInRows = true;
            }
        }

        return $stringInRows;
    }

    /**
     * Replace characters.
     *
     * @param string $data
     * @param mixed[] $replaces
     * @return mixed
     */
    protected function replaceCharacters(string $data, array $replaces)
    {
        $data = mb_strtoupper($data);
        $data = str_replace(
            Constants::CHARACTERS,
            '_',
            $data
        );

        foreach ($replaces as $from => $to) {
            $data = str_replace(mb_strtoupper($from), mb_strtoupper($to), $data);
        }

        return $data;
    }
}
