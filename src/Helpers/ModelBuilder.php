<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Helpers;

use CoRex\Laravel\Model\Builders\ClassExtendsBuilder;
use CoRex\Laravel\Model\Builders\ConstantsBuilder;
use CoRex\Laravel\Model\Builders\DatabaseInformationBuilder;
use CoRex\Laravel\Model\Builders\DeclareStrictBuilder;
use CoRex\Laravel\Model\Builders\NamespaceBuilder;
use CoRex\Laravel\Model\Builders\PhpDocBuilder;
use CoRex\Laravel\Model\Builders\PreservedLinesBuilder;
use CoRex\Laravel\Model\Builders\StatementGroupEndBuilder;
use CoRex\Laravel\Model\Builders\StatementGroupStartBuilder;
use CoRex\Laravel\Model\Builders\TimestampsBuilder;
use CoRex\Laravel\Model\Builders\TraitBuilder;
use CoRex\Laravel\Model\Builders\UsesBuilder;
use CoRex\Laravel\Model\Exceptions\BuilderException;
use CoRex\Laravel\Model\Interfaces\BuilderInterface;
use CoRex\Laravel\Model\Interfaces\ConfigInterface;
use CoRex\Laravel\Model\Interfaces\DatabaseInterface;
use CoRex\Laravel\Model\Interfaces\ModelBuilderInterface;
use CoRex\Laravel\Model\Interfaces\ParserInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;

class ModelBuilder implements ModelBuilderInterface
{
    /** @var Application */
    private $application;

    /** @var ConfigInterface */
    private $config;

    /** @var string */
    private $connection;

    /** @var string */
    private $table;

    /** @var string */
    private $filename;

    /** @var string */
    private $class;

    /** @var ParserInterface */
    private $parser;

    /** @var DatabaseInterface */
    private $database;

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
     * Set config.
     *
     * @param ConfigInterface $config
     */
    public function setConfig(ConfigInterface $config): void
    {
        $this->config = $config;
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
     * Set table.
     *
     * @param string $connection
     * @param string $table
     * @throws BindingResolutionException
     * @throws BuilderException
     */
    public function setTable(string $connection, string $table): void
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->class = Str::studly($this->table);

        // Build filename.
        $this->buildFilename();

        // Validate table existence.
        if (!$this->database->hasTable($this->table)) {
            throw new BuilderException('Table [' . $this->table . '] not found.');
        }

        // Set parser.
        $this->parser = $this->application->make(Parser::class);
        $this->parser->setFilename($this->filename);
    }

    /**
     * Build.
     *
     * @return string
     * @throws BindingResolutionException
     */
    public function build(): string
    {
        // Setup builders.
        $builders = [
            $this->application->make(DeclareStrictBuilder::class),
            $this->application->make(NamespaceBuilder::class),
            $this->application->make(UsesBuilder::class),
            $this->application->make(PhpDocBuilder::class),
            $this->application->make(ClassExtendsBuilder::class),
            $this->application->make(StatementGroupStartBuilder::class),
            $this->application->make(TraitBuilder::class),
            $this->application->make(ConstantsBuilder::class),
            $this->application->make(TimestampsBuilder::class),
            $this->application->make(DatabaseInformationBuilder::class),
            $this->application->make(PreservedLinesBuilder::class),
            $this->application->make(StatementGroupEndBuilder::class),
        ];

        // Build model lines.
        $modelLines = [
            '<?php',
            '',
        ];
        foreach ($builders as $builder) {
            $this->executeBuilder($modelLines, $builder);
        }

        return implode("\n", $modelLines);
    }

    /**
     * Get model filename.
     *
     * @return string
     */
    public function getModelFilename(): string
    {
        return $this->filename;
    }

    /**
     * Get model namespace filename.
     *
     * @return string
     */
    public function getModelNamespaceFilename(): string
    {
        $parts = [$this->config->getNamespace()];

        if ($this->config->getAddConnectionToNamespace()) {
            $parts[] = ucfirst($this->connection);
        }

        $parts[] = Str::studly($this->table);

        return implode('\\', $parts);
    }

    /**
     * Get config.
     *
     * @return ConfigInterface
     */
    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * Get parser.
     *
     * @return ParserInterface
     */
    public function getParser(): ParserInterface
    {
        return $this->parser;
    }

    /**
     * Get database.
     *
     * @return DatabaseInterface
     */
    public function getDatabase(): DatabaseInterface
    {
        return $this->database;
    }

    /**
     * Get connection.
     *
     * @return string
     */
    public function getConnection(): string
    {
        return $this->connection;
    }

    /**
     * Get table.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Get class.
     *
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Build filename.
     */
    private function buildFilename(): void
    {
        $parts = [$this->config->getPath()];

        if ($this->config->getAddConnectionToNamespace()) {
            $parts[] = ucfirst($this->connection);
        }

        $parts[] = Str::studly($this->table) . '.php';

        $this->filename = implode('/', $parts);
    }

    /**
     * Execute builder.
     *
     * @param mixed[] $content
     * @param BuilderInterface $builder
     */
    private function executeBuilder(array &$content, BuilderInterface $builder): void
    {
        $builder->setModelBuilder($this);
        $content = array_merge($content, $builder->build());
    }
}
