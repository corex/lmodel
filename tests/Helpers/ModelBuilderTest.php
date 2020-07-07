<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Helpers;

use CoRex\Helpers\Obj;
use CoRex\Laravel\Model\Constants;
use CoRex\Laravel\Model\Exceptions\BuilderException;
use CoRex\Laravel\Model\Helpers\ModelBuilder;
use CoRex\Laravel\Model\Interfaces\ConfigInterface;
use CoRex\Laravel\Model\Interfaces\DatabaseInterface;
use CoRex\Laravel\Model\Interfaces\ParserInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;
use ReflectionException;
use Tests\CoRex\Laravel\Model\TestBase;

class ModelBuilderTest extends TestBase
{
    /**
     * Test set application.
     *
     * @throws ReflectionException
     */
    public function testSetApplication(): void
    {
        $this->assertSame($this->application, Obj::getProperty('application', $this->modelBuilder));
    }

    /**
     * Test set config.
     *
     * @throws ReflectionException
     */
    public function testSetConfig(): void
    {
        $this->assertSame($this->config, Obj::getProperty('config', $this->modelBuilder));
    }

    /**
     * Test set database.
     *
     * @throws ReflectionException
     */
    public function testSetDatabase(): void
    {
        $this->assertInstanceOf(DatabaseInterface::class, Obj::getProperty('database', $this->modelBuilder));
    }

    /**
     * Test set table.
     *
     * @throws ReflectionException
     */
    public function testSetTable(): void
    {
        $this->assertSame($this->connection, Obj::getProperty('connection', $this->modelBuilder));
        $this->assertSame($this->table, Obj::getProperty('table', $this->modelBuilder));
        $this->assertInstanceOf(ParserInterface::class, Obj::getProperty('parser', $this->modelBuilder));
    }

    /**
     * Test constructor table not found.
     *
     * @throws BuilderException
     */
    public function testConstructorTableNotFound(): void
    {
        $this->expectException(BuilderException::class);
        $this->expectExceptionMessage('Table [unknown] not found.');
        $this->modelBuilder = new ModelBuilder();
        $this->modelBuilder->setApplication($this->application);
        $this->modelBuilder->setConfig($this->config);
        $this->modelBuilder->setDatabase($this->database);
        $this->modelBuilder->setTable($this->connection, 'unknown');
    }

    /**
     * Test build.
     *
     * @throws BindingResolutionException
     */
    public function testBuild(): void
    {
        $this->assertStringContainsString(Constants::PRESERVED_IDENTIFIER, $this->modelBuilder->build());
    }

    /**
     * Test get model filename.
     */
    public function testGetModelFilename(): void
    {
        $parts = [$this->config->getPath()];

        if ($this->config->getAddConnectionToNamespace()) {
            $parts[] = ucfirst($this->connection);
        }

        $parts[] = Str::studly($this->table) . '.php';

        $filename = implode('/', $parts);

        $this->assertSame($filename, $this->modelBuilder->getModelFilename());
    }

    /**
     * Test get model filename.
     */
    public function testGetModelNamespaceFilename(): void
    {
        $parts = [$this->config->getNamespace()];

        if ($this->config->getAddConnectionToNamespace()) {
            $parts[] = ucfirst($this->connection);
        }

        $parts[] = Str::studly($this->table);

        $modelFilename = implode('\\', $parts);

        $this->assertSame($modelFilename, $this->modelBuilder->getModelNamespaceFilename());
    }

    /**
     * Test get config.
     */
    public function testGetConfig(): void
    {
        $this->assertInstanceOf(ConfigInterface::class, $this->modelBuilder->getConfig());
    }

    /**
     * Test get parser.
     */
    public function testGetParser(): void
    {
        $this->assertInstanceOf(ParserInterface::class, $this->modelBuilder->getParser());
    }

    /**
     * Test get database.
     */
    public function testGetDatabase(): void
    {
        $this->assertInstanceOf(DatabaseInterface::class, $this->modelBuilder->getDatabase());
    }

    /**
     * Test get connection.
     */
    public function testGetConnection(): void
    {
        $this->assertSame('testbench', $this->modelBuilder->getConnection());
    }

    /**
     * Test get table.
     */
    public function testGetTable(): void
    {
        $this->assertSame('lmodel', $this->modelBuilder->getTable());
    }

    /**
     * Test get class.
     */
    public function testGetClass(): void
    {
        $this->assertSame('Lmodel', $this->modelBuilder->getClass());
    }
}
