<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Helpers;

use CoRex\Laravel\Model\Constants;
use CoRex\Laravel\Model\Exceptions\ConfigException;
use CoRex\Laravel\Model\Helpers\Definitions\TableDefinition;
use CoRex\Laravel\Model\Interfaces\ConfigInterface;

class Config implements ConfigInterface
{
    /** @var mixed[] */
    private $data;

    /**
     * Config constructor.
     *
     * @param mixed[] $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Validate.
     *
     * @throws ConfigException
     */
    public function validate(): bool
    {
        // Validate that configuration has been specified.
        if (count($this->data) === 0) {
            throw new ConfigException('Configuration not specified.');
        }

        // Validate declare strict.
        if ($this->get('declareStrict') === null) {
            $this->throwConfigException('[declareStrict] not set.');
        }

        // Validate path.
        if ($this->get('path') === null) {
            $this->throwConfigException('[path] not set.');
        }

        // Validate namespace.
        if ($this->get('namespace') === null) {
            $this->throwConfigException('[namespace] not set.');
        }

        // Validate addNamespaceConnection.
        if ($this->get('addConnectionToNamespace') === null) {
            $this->throwConfigException('[addConnectionToNamespace] not set.');
        }

        // Validate addDatabaseConnection.
        if ($this->get('addDatabaseConnection') === null) {
            $this->throwConfigException('[addDatabaseConnection] not set.');
        }

        // Validate addDatabaseTable.
        if ($this->get('addDatabaseTable') === null) {
            $this->throwConfigException('[addDatabaseTable] not set.');
        }

        // Validate extend.
        $extends = $this->get('extends');
        if ($extends === null || trim((string)$extends) === '') {
            $this->throwConfigException('[extends] not set.');
        }

        return true;
    }

    /**
     * Get declare strict.
     *
     * @return bool
     */
    public function getDeclareStrict(): bool
    {
        return $this->get('declareStrict') === true;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->get('path');
    }

    /**
     * Get namespace.
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->get('namespace');
    }

    /**
     * Get add connection to namespace.
     *
     * @return bool
     */
    public function getAddConnectionToNamespace(): bool
    {
        return $this->get('addConnectionToNamespace') === true;
    }

    /**
     * Get add database connection.
     *
     * @return bool
     */
    public function getAddDatabaseConnection(): bool
    {
        return $this->get('addDatabaseConnection') === true;
    }

    /**
     * Get add database table.
     *
     * @return bool
     */
    public function getAddDatabaseTable(): bool
    {
        return $this->get('addDatabaseTable') === true;
    }

    /**
     * Get extends.
     *
     * @param string|null $default
     * @return string|null ?string
     */
    public function getExtends(?string $default = null): ?string
    {
        return $this->get('extends', $default);
    }

    /**
     * Get indent.
     *
     * @return string|null
     */
    public function getIndent(): ?string
    {
        return $this->get('indent');
    }

    /**
     * Get max line length.
     *
     * @return int
     */
    public function getMaxLineLength(): int
    {
        return $this->get('maxLineLength', Constants::MAX_LINE_LENGTH);
    }

    /**
     * Get Doctrine mappings.
     *
     * @return mixed[]
     */
    public function getDoctrineMappings(): array
    {
        return $this->get('doctrine', []);
    }

    /**
     * Get PhpDoc mappings.
     *
     * @return mixed[]
     */
    public function getPhpDocMappings(): array
    {
        return array_merge(
            Constants::STANDARD_COLUMN_MAPPINGS,
            $this->get('phpdoc', [])
        );
    }

    /**
     * Get builder mappings.
     *
     * @return mixed[]
     */
    public function getBuilderMappings(): array
    {
        return $this->get('builders', []);
    }

    /**
     * Get ignored tables.
     *
     * @param string $connection
     * @return string[]
     */
    public function getIgnoredTables(string $connection): array
    {
        return (array)$this->get('ignored.' . $connection, []);
    }

    /**
     * Get table definition.
     *
     * @param string $connection
     * @param string $table
     * @return TableDefinition
     */
    public function getTableDefinition(string $connection, string $table): TableDefinition
    {
        $data = $this->get('tables.' . $connection . '.' . $table, []);

        return new TableDefinition($data);
    }

    /**
     * To array.
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Value.
     *
     * @param string $key
     * @param mixed $default Default null.
     * @return mixed
     */
    private function get(string $key, $default = null)
    {
        $keyParts = trim($key) !== '' ? explode('.', $key) : [];
        $keyArray = array_pop($keyParts);

        // Get data.
        $data = $this->getData($keyParts);
        if (array_key_exists($keyArray, $data)) {
            return $data[$keyArray];
        }

        return $default;
    }

    /**
     * Throw config exception.
     *
     * @param string $message
     * @throws ConfigException
     */
    private function throwConfigException(string $message): void
    {
        throw new ConfigException('Configuration: ' . $message);
    }

    /**
     * Get data.
     *
     * @param mixed[] $keyParts
     * @return mixed[]
     */
    private function getData(array $keyParts): array
    {
        $data = $this->data;

        if (count($keyParts) === 0) {
            return $data;
        }

        foreach ($keyParts as $keyPart) {
            // If key does not exist, return empty array for further processing.
            if (!is_array($data) || !array_key_exists($keyPart, $data)) {
                return [];
            }

            // Dig deeper.
            $data = $data[$keyPart];
        }

        return $data;
    }
}
