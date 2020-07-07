<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Helpers;

use CoRex\Helpers\Obj;
use CoRex\Laravel\Model\Helpers\Database;
use CoRex\Laravel\Model\Helpers\Database\Column;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase;
use ReflectionException;

class DatabaseTest extends TestCase
{
    /** @var Database */
    private $database;

    /**
     * Test set connection.
     *
     * @throws ReflectionException
     */
    public function testSetConnection(): void
    {
        $check = md5((string)random_int(1, 100000));
        $this->database->setConnection($check);
        $this->assertSame($check, Obj::getProperty('connection', $this->database));
    }

    /**
     * Test register Doctrine type mapping when not registered.
     */
    public function testRegisterDoctrineTypeMappingNotRegistered(): void
    {
        $this->expectException(DBALException::class);
        $this->expectExceptionMessage(
            'Unknown database type enum requested, Doctrine\DBAL\Platforms\SqlitePlatform may not support it.'
        );
        $schemaManager = DB::connection()->getDoctrineSchemaManager();
        $databasePlatform = $schemaManager->getDatabasePlatform();
        $databasePlatform->getDoctrineTypeMapping('enum');
    }

    /**
     * Test register Doctrine type mapping.
     *
     * @throws DBALException
     */
    public function testRegisterDoctrineTypeMapping(): void
    {
        $this->database->registerDoctrineTypeMapping('enum', 'string');

        $schemaManager = DB::connection()->getDoctrineSchemaManager();
        $databasePlatform = $schemaManager->getDatabasePlatform();
        $this->assertSame('string', $databasePlatform->getDoctrineTypeMapping('enum'));
    }

    /**
     * Test get tables.
     */
    public function testGetTables(): void
    {
        $tables = $this->database->getTables();
        $this->assertCount(3, $tables);
        $this->assertTrue(in_array('lmodel', $tables, true));
        $this->assertTrue(in_array('ltest', $tables, true));
        $this->assertTrue(in_array('migrations', $tables, true));
    }

    /**
     * Test has table.
     */
    public function testHasTable(): void
    {
        $this->assertTrue($this->database->hasTable('lmodel'));
        $this->assertTrue($this->database->hasTable('ltest'));
        $this->assertFalse($this->database->hasTable('unknown'));
    }

    /**
     * Test get columns.
     */
    public function testGetColumns(): void
    {
        // Assert unknown table.
        $this->assertSame([], $this->database->getColumns('unknown'));

        $columns = $this->database->getColumns('lmodel');

        // Validate column names.
        $columnNames = array_keys($columns);
        $validColumnNames = ['id', 'code', 'number', 'string', 'status', 'created_at_test', 'updated_at_test'];
        foreach ($validColumnNames as $validColumnName) {
            $this->assertTrue(in_array($validColumnName, $columnNames, true));
        }

        // Valdate column instances.
        foreach ($columns as $column) {
            $this->assertInstanceOf(Column::class, $column);
        }
    }

    /**
     * Test get rows.
     */
    public function testGetRows(): void
    {
        $this->assertSame([], $this->database->getRows('unknown', 'unknown', 'unknown'));

        $rows = $this->database->getRows('lmodel', 'code', 'number');

        // Validate values.
        for ($counter = 1; $counter <= 10; $counter++) {
            $this->assertTrue(in_array((string)$counter, $rows, true));
        }

        // Validate keys.
        foreach ($rows as $key => $value) {
            $this->assertSame('Code ' . $value, $key);
        }
    }

    /**
     * Test get schema manager.
     */
    public function testGetSchemaManager(): void
    {
        $this->assertInstanceOf(AbstractSchemaManager::class, $this->database->getSchemaManager());
    }

    /**
     * Test get database platform.
     */
    public function testGetDatabasePlatform(): void
    {
        $this->assertInstanceOf(AbstractPlatform::class, $this->database->getDatabasePlatform());
    }

    /**
     * Define environment setup.
     *
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app): void
    {
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
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->database = new Database();
        $this->loadMigrationsFrom(dirname(__DIR__, 2) . '/tests/migrations');
    }
}
