<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model;

use CoRex\Helpers\Obj;
use CoRex\Laravel\Model\Constants;
use CoRex\Laravel\Model\Helpers\Config;
use CoRex\Laravel\Model\Helpers\Database;
use CoRex\Laravel\Model\Helpers\ModelBuilder;
use CoRex\Laravel\Model\Interfaces\BuilderInterface;
use CoRex\Laravel\Model\Interfaces\ConfigInterface;
use CoRex\Laravel\Model\Interfaces\DatabaseInterface;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase;
use ReflectionException;

abstract class TestBase extends TestCase
{
    /** @var ModelBuilder */
    protected $modelBuilder;

    /** @var Application */
    protected $application;

    /** @var ConfigInterface */
    protected $config;

    /** @var DatabaseInterface */
    protected $database;

    /** @var string */
    protected $connection;

    /** @var string */
    protected $table;

    /**
     * Define environment setup.
     *
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $this->application = $app;

        parent::getEnvironmentSetUp($app);

        $app['config']->set('database.default', 'testbench');
        $app['config']->set(
            'database.connections.testbench',
            [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]
        );
    }

    /**
     * Create builder.
     *
     * @param string $builderClass
     * @return BuilderInterface
     */
    protected function createBuilder(string $builderClass): BuilderInterface
    {
        $builder = new $builderClass();
        $builder->setModelBuilder($this->modelBuilder);

        return $builder;
    }

    /**
     * Set config.
     *
     * @param string $key
     * @param mixed $value
     * @throws ReflectionException
     */
    protected function setConfig(string $key, $value): void
    {
        $data = Obj::getProperty('data', $this->config);
        $data[$key] = $value;
        Obj::setProperty('data', $this->config, $data);
    }

    /**
     * Get config.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     * @throws ReflectionException
     */
    protected function getConfig(string $key, $default = null)
    {
        $data = Obj::getProperty('data', $this->config);
        if (array_key_exists($key, $data)) {
            return $data[$key];
        }

        return $default;
    }

    /**
     * Assert lines contains.
     *
     * @param string $needle
     * @param string[] $lines
     */
    protected function assertLinesContains(string $needle, array $lines): void
    {
        $message = 'Failed asserting one or more lines contains "' . $needle . '"';
        $this->assertTrue($this->linesContains([$needle], $lines), $message);
    }

    /**
     * Assert lines not contains.
     *
     * @param string $needle
     * @param string[] $lines
     */
    protected function assertLinesNotContains(string $needle, array $lines): void
    {
        $message = 'Failed asserting one or more lines not contains "' . $needle . '"';
        $this->assertFalse($this->linesContains([$needle], $lines), $message);
    }

    /**
     * Assert lines contains lines.
     *
     * @param array $needles
     * @param array $lines
     */
    protected function assertLinesContainsLines(array $needles, array $lines): void
    {
        $message = 'Failed asserting one or more lines contains "[multiple lines]".';
        $this->assertTrue($this->linesContains($needles, $lines), $message);
    }

    /**
     * Assert lines not contains lines.
     *
     * @param array $needles
     * @param array $lines
     */
    protected function assertLinesNotContainsLines(array $needles, array $lines): void
    {
        $message = 'Failed asserting one or more lines not contains "[multiple lines]"';
        $this->assertFalse($this->linesContains($needles, $lines), $message);
    }

    /**
     * Get indentation.
     *
     * @param int $count
     * @return string
     */
    protected function indent(int $count): string
    {
        $indent = $this->modelBuilder->getConfig()->getIndent();
        if ($indent === null) {
            $indent = Constants::DEFAULT_INDENT;
        }

        return str_repeat($indent, $count);
    }

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $testData = TestData::getConfig();

        $this->loadMigrationsFrom(dirname(__DIR__) . '/tests/migrations');

        // Create configuration.
        $this->config = new Config($testData);

        $this->database = new Database();
        $this->database->setConnection('testbench');

        // Create connection name.
        $this->connection = 'testbench';

        // Create table name.
        $this->table = 'lmodel';

        // Create model builder.
        $this->modelBuilder = new ModelBuilder();
        $this->modelBuilder->setApplication($this->application);
        $this->modelBuilder->setConfig($this->config);
        $this->modelBuilder->setDatabase($this->database);
        $this->modelBuilder->setTable($this->connection, $this->table);
    }

    /**
     * Has lines.
     *
     * @param array $needles
     * @param string[] $lines
     * @return bool
     */
    private function linesContains(array $needles, array $lines): bool
    {
        $found = true;
        foreach ($needles as $needle) {
            if (trim($needle) === '') {
                continue;
            }

            if (array_search($needle, $lines, true) === false) {
                $found = false;
            }
        }

        return $found;
    }
}
