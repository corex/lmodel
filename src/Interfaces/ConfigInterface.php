<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Interfaces;

use CoRex\Laravel\Model\Exceptions\ConfigException;
use CoRex\Laravel\Model\Helpers\Definitions\TableDefinition;

interface ConfigInterface
{
    /**
     * Validate.
     *
     * @throws ConfigException
     */
    public function validate(): bool;

    /**
     * Get declare strict.
     *
     * @return bool
     */
    public function getDeclareStrict(): bool;

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Get namespace.
     *
     * @return string
     */
    public function getNamespace(): string;

    /**
     * Get add connection to namespace.
     *
     * @return bool
     */
    public function getAddConnectionToNamespace(): bool;

    /**
     * Get add database connection.
     *
     * @return bool
     */
    public function getAddDatabaseConnection(): bool;

    /**
     * Get add database table.
     *
     * @return bool
     */
    public function getAddDatabaseTable(): bool;

    /**
     * Get extends.
     *
     * @param string|null $default
     * @return string|null ?string
     */
    public function getExtends(?string $default = null): ?string;

    /**
     * Get indent.
     *
     * @return string|null
     */
    public function getIndent(): ?string;

    /**
     * Get max line length.
     *
     * @return int
     */
    public function getMaxLineLength(): int;

    /**
     * Get Doctrine mappings.
     *
     * @return mixed[]
     */
    public function getDoctrineMappings(): array;

    /**
     * Get PhpDoc mappings.
     *
     * @return mixed[]
     */
    public function getPhpDocMappings(): array;

    /**
     * Get builder mappings.
     *
     * @return mixed[]
     */
    public function getBuilderMappings(): array;

    /**
     * Get ignored tables.
     *
     * @param string $connection
     * @return string[]
     */
    public function getIgnoredTables(string $connection): array;

    /**
     * Get table definition.
     *
     * @param string $connection
     * @param string $table
     * @return TableDefinition
     */
    public function getTableDefinition(string $connection, string $table): TableDefinition;

    /**
     * To array.
     *
     * @return mixed[]
     */
    public function toArray(): array;
}
