<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Helpers;

use CoRex\Laravel\Model\Exceptions\ConfigException;
use CoRex\Laravel\Model\Exceptions\WriterException;
use CoRex\Laravel\Model\Interfaces\ConfigInterface;
use CoRex\Laravel\Model\Interfaces\DatabaseInterface;
use CoRex\Laravel\Model\Interfaces\ModelBuilderInterface;
use CoRex\Laravel\Model\Interfaces\ModelsBuilderInterface;
use CoRex\Laravel\Model\Interfaces\WriterInterface;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;

class ModelsBuilder implements ModelsBuilderInterface
{
    /** @var Application */
    private $application;

    /** @var ConfigInterface */
    private $config;

    /** @var WriterInterface */
    private $writer;

    /** @var DatabaseInterface */
    private $database;

    /** @var OutputStyle */
    private $output;

    /** @var string */
    private $connection;

    /** @var string[] */
    private $tables;

    /**
     * Set config.
     *
     * @param ConfigInterface $config
     */
    public function setConfig(ConfigInterface $config): void
    {
        $this->config = $config;
    }

    /**
     * Set writer.
     *
     * @param WriterInterface $writer
     */
    public function setWriter(WriterInterface $writer): void
    {
        $this->writer = $writer;
    }

    /**
     * Set database.
     *
     * @param DatabaseInterface $database
     */
    public function setDatabase(DatabaseInterface $database): void
    {
        $this->database = $database;
    }

    /**
     * Set application.
     *
     * @param Application $application
     */
    public function setApplication(Application $application): void
    {
        $this->application = $application;
    }

    /**
     * Set output.
     *
     * @param OutputStyle $output
     */
    public function setOutput(OutputStyle $output): void
    {
        $this->output = $output;
    }

    /**
     * Execute.
     *
     * @param mixed[] $arguments
     * @param array $options
     * @throws BindingResolutionException
     * @throws ConfigException
     * @throws WriterException
     */
    public function execute(array $arguments, array $options): void
    {
        // Extract options.
        $showDestination = $options['destination'] ?? false;
        $showConsole = $options['console'] ?? false;

        if (!$showDestination && !$showConsole) {
            $this->output('Create/update model(s) from existing schema', 'info');
            $this->output('');
        }

        $this->setConnection($arguments);
        $this->setTables($arguments);

        $this->config->validate();
        $this->registerDoctrineMappings();
        $this->registerCustomBuilders();

        // Make models.
        $packageDefinitions = $this->config->getPackageDefinitions();
        $numberOfModelsGenerated = 0;
        if (count($this->tables) > 0) {
            foreach ($this->tables as $table) {
                // Setup and execute model builder.
                $this->application->bindIf(ModelBuilderInterface::class, ModelBuilder::class);
                $modelBuilder = $this->application->make(ModelBuilderInterface::class);

                // Set package definition by table.
                $packageDefinition = $packageDefinitions->getPackageMatch($table);
                if ($packageDefinition !== null) {
                    $modelBuilder->setPackageDefinition($packageDefinition);
                }

                $modelBuilder->setApplication($this->application);
                $modelBuilder->setConfig($this->config);
                $modelBuilder->setDatabase($this->database);
                $modelBuilder->setTable($this->connection, $table);

                $content = $modelBuilder->build();

                // Write model to disk.
                $filename = $modelBuilder->getModelFilename();
                $namespaceTable = $modelBuilder->getModelNamespaceFilename();
                if (!$showDestination && !$showConsole) {
                    $this->writer->clearContent();
                    $this->writer->setFilename($filename);
                    $this->writer->setContent($content);
                    $this->writer->write(true);
                } else {
                    // Show destination.
                    if ($showDestination) {
                        $this->output('Destination: ' . $namespaceTable . ' -> ' . $filename, 'info');
                    }

                    // Show console.
                    if ($showConsole) {
                        $this->output($content);
                        $this->output('');
                    }
                }

                if (!$showDestination && !$showConsole) {
                    $this->output('Model [' . $namespaceTable . '] generated.', 'info');
                }

                $numberOfModelsGenerated++;
            }
        }

        if (!$showDestination && !$showConsole) {
            $this->output('');
            $this->output($numberOfModelsGenerated . ' model(s) generated.', 'info');
        }
    }

    /**
     * Register Doctrine mappings.
     */
    private function registerDoctrineMappings(): void
    {
        $doctrineTypeMappings = array_merge(
            [
                'enum' => 'string',
            ],
            $this->config->getDoctrineMappings()
        );
        foreach ($doctrineTypeMappings as $databaseType => $doctrineType) {
            $this->database->registerDoctrineTypeMapping($databaseType, $doctrineType);
        }
    }

    /**
     * Register custom builders.
     */
    private function registerCustomBuilders(): void
    {
        $builderMappings = $this->config->getBuilderMappings();
        foreach ($builderMappings as $newBuilder => $existingBuilder) {
            $this->application->bindIf($existingBuilder, $newBuilder);
        }
    }

    /**
     * Output.
     *
     * @param string $output
     * @param string|null $style
     */
    private function output(string $output, ?string $style = null): void
    {
        if ($style !== null) {
            $output = '<' . $style . '>' . $output . '</' . $style . '>';
        }

        $this->output->writeln($output);
    }

    /**
     * Set connection.
     *
     * @param mixed[] $arguments
     * @throws ConfigException
     */
    private function setConnection(array $arguments): void
    {
        // Prepare connection/connections.
        $connection = $arguments['connection'] ?? null;
        $connections = array_keys($this->laravelConfig('database.connections'));
        if ($connection === '.') {
            $connection = $this->laravelConfig('database.default');
        }

        // Validate connection existence.
        if (!in_array($connection, $connections, true)) {
            $message = 'Connection ' . $connection . ' not found.';
            $message .= ' Available connections: ' . implode(', ', $connections) . '.';

            throw new ConfigException($message);
        }

        // Set connection.
        $this->connection = $connection;
    }

    /**
     * Set tables.
     *
     * @param mixed[] $arguments
     * @throws ConfigException
     */
    private function setTables(array $arguments): void
    {
        $tables = $arguments['tables'] ?? null;
        $ignoredTables = $this->config->getIgnoredTables($this->connection);
        $existingTables = $this->database->getTables();

        // Prepare list of tables.
        if ($tables !== '.') {
            $tables = explode(',', $tables);
        } else {
            $tables = $existingTables;
        }

        // Build list of tables.
        $result = [];
        if (count($tables) > 0) {
            foreach ($tables as $table) {
                // Validate table existence.
                if (!in_array($table, $existingTables, true)) {
                    throw new ConfigException('Table ' . $table . ' not found.');
                }

                // Check if table is ignored.
                if (in_array($table, $ignoredTables, true)) {
                    $this->output('<fg=yellow>Table ' . $table . ' ignored.</>');

                    continue;
                }

                $result[] = $table;
            }
        }

        // Remove migrations table from list.
        $migrationsTable = $this->laravelConfig('database.migrations');
        if (in_array($migrationsTable, $result, true)) {
            $index = array_search($migrationsTable, $result, true);
            if ($index !== false) {
                unset($result[$index]);
            }
        }

        $this->tables = $result;
    }

    /**
     * Laravel config.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    private function laravelConfig(string $key, $default = null)
    {
        return $this->application->get('config')->get($key, $default);
    }
}
