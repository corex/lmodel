<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Interfaces;

use CoRex\Laravel\Model\Helpers\Definitions\PackageDefinition;
use CoRex\Laravel\Model\Helpers\Definitions\TableDefinition;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;

interface ModelBuilderInterface
{
    /**
     * Set application.
     *
     * @param Application $application
     */
    public function setApplication(Application $application): void;

    /**
     * Set config.
     *
     * @param ConfigInterface $config
     */
    public function setConfig(ConfigInterface $config): void;

    /**
     * Set package definition.
     *
     * @param PackageDefinition $packageDefinition
     */
    public function setPackageDefinition(PackageDefinition $packageDefinition): void;

    /**
     * Set database.
     *
     * @param DatabaseInterface $database
     */
    public function setDatabase(DatabaseInterface $database): void;

    /**
     * Set table.
     *
     * @param string $connection
     * @param string $table
     */
    public function setTable(string $connection, string $table): void;

    /**
     * Build.
     *
     * @return string
     * @throws BindingResolutionException
     */
    public function build(): string;

    /**
     * Get model filename.
     *
     * @return string
     */
    public function getModelFilename(): string;

    /**
     * Get model namespace filename.
     *
     * @return string
     */
    public function getModelNamespaceFilename(): string;

    /**
     * Get config.
     *
     * @return ConfigInterface
     */
    public function getConfig(): ConfigInterface;

    /**
     * Get parser.
     *
     * @return ParserInterface
     */
    public function getParser(): ParserInterface;

    /**
     * Get database.
     *
     * @return DatabaseInterface
     */
    public function getDatabase(): DatabaseInterface;

    /**
     * Get package definition.
     *
     * @return PackageDefinition|null
     */
    public function getPackageDefinition(): ?PackageDefinition;

    /**
     * Get table definition.
     *
     * @return TableDefinition
     */
    public function getTableDefinition(): TableDefinition;

    /**
     * Get connection.
     *
     * @return string
     */
    public function getConnection(): string;

    /**
     * Get table.
     *
     * @return string
     */
    public function getTable(): string;

    /**
     * Get class.
     *
     * @return string
     */
    public function getClass(): string;
}
