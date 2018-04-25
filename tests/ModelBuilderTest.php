<?php

use CoRex\Laravel\Model\ModelBuilder;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase;

class ModelBuilderTest extends TestCase
{
    private $connection = 'test';
    private $table = 'status';

    /**
     * Test constructor connection not found.
     * @throws Exception
     */
    public function testConstructorConnectionNotFound()
    {
        $this->app['config']->set('database.default', '');
        $this->app['config']->set('database.connections', []);
        $connection = md5(microtime());
        $table = md5(microtime());
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Database [' . $connection . '] not configured.');
        new ModelBuilder($connection, $table);
    }

    /**
     * Test constructor table not found.
     * @throws Exception
     */
    public function testConstructorTableNotFound()
    {
        $table = md5(microtime());
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Table [' . $table . '] not found.');
        new ModelBuilder($this->connection, $table);
    }

    /**
     * Test constructor table found.
     * @throws Exception
     */
    public function testConstructorTableFound()
    {
        // Create and make sure table exists.
        $this->assertFalse($this->getDoctrineSchemaManager()->tablesExist([$this->table]));
        $this->createTable();
        $this->assertTrue($this->getDoctrineSchemaManager()->tablesExist([$this->table]));

        // Create instance of model builder and get schema for table.
        $modelBuilder = new ModelBuilder($this->connection, $this->table);
        $reflectionClass = new ReflectionClass($modelBuilder);
        $modelBuilderProperty = $reflectionClass->getProperty('schema');
        $modelBuilderProperty->setAccessible(true);
        $modelBuilderSchema = $modelBuilderProperty->getValue($modelBuilder);

        $schema = $this->getDoctrineSchemaManager()->listTableDetails($this->table);

        $this->assertEquals($schema, $modelBuilderSchema);
    }

    /**
     * Test get model filename not addConnection.
     * @throws Exception
     */
    public function testGetModelFilenameNotAddConnection()
    {
        $check = md5(microtime());
        $this->app['config']->set('corex', [
            'lmodel' => [
                'path' => $check,
                'namespace' => $check,
                'addConnection' => false
            ]
        ]);
        $this->createTable();
        $modelBuilder = new ModelBuilder($this->connection, $this->table);
        $filename = $modelBuilder->getModelFilename();

        $parts = explode('/', $filename);
        $this->assertEquals($check, $parts[0]);
        $this->assertEquals(ucfirst($this->table) . '.php', $parts[1]);
    }

    /**
     * Test get model filename addConnection.
     * @throws Exception
     */
    public function testGetModelFilenameAddConnection()
    {
        $check = md5(microtime());
        $this->app['config']->set('corex', [
            'lmodel' => [
                'path' => $check,
                'namespace' => $check,
                'addConnection' => true
            ]
        ]);
        $this->createTable();
        $modelBuilder = new ModelBuilder($this->connection, $this->table);
        $filename = $modelBuilder->getModelFilename();

        $parts = explode('/', $filename);
        $this->assertEquals($check, $parts[0]);
        $this->assertEquals(ucfirst($this->connection), $parts[1]);
        $this->assertEquals(ucfirst($this->table) . '.php', $parts[2]);
    }

    /**
     * Test set guarded attributes.
     * @throws Exception
     */
    public function testSetGuardedAttributes()
    {
        $this->createTable();
        $check1 = md5(microtime());
        $check2 = md5(microtime());
        $modelBuilder = new ModelBuilder($this->connection, $this->table);
        $modelBuilder->setGuardedAttributes([
            'check1' => $check1,
            'check2' => $check2
        ]);
        $guardedAttributes = $this->getProperty('guardedAttributes', $modelBuilder);
        $this->assertEquals([
            'check1' => $check1,
            'check2' => $check2
        ], $guardedAttributes);
    }

    /**
     * Test get fillable attributes all.
     * @throws Exception
     */
    public function testGetFillableAttributesAll()
    {
        $this->createTable();
        $modelBuilder = new ModelBuilder($this->connection, $this->table);
        $fillableAttributes = $this->callPrivateMethod('getFillableAttributes', $modelBuilder);
        $this->assertEquals(['id', 'firstname', 'lastname', 'status'], $fillableAttributes);
    }

    /**
     * Test get fillable attributes guarded.
     * @throws Exception
     */
    public function testGetFillableAttributesGuarded()
    {
        $this->createTable();
        $modelBuilder = new ModelBuilder($this->connection, $this->table);
        $modelBuilder->setGuardedAttributes(['id']);
        $fillableAttributes = $this->callPrivateMethod('getFillableAttributes', $modelBuilder);
        $this->assertEquals(['firstname', 'lastname', 'status'], $fillableAttributes);
    }

    /**
     * Test get tokens.
     * @throws Exception
     */
    public function testGetTokens()
    {
        $check = md5(microtime());
        $this->app['config']->set('corex', [
            'lmodel' => [
                'path' => $check,
                'namespace' => $check,
                'addConnection' => true,
                'const' => [
                    $this->connection => [
                        $this->table => [
                            'id' => 'id',
                            'name' => 'firstname',
                            'prefix' => 'C'
                        ]
                    ]
                ]
            ]
        ]);
        $this->createTable();

        // Insert test data 1.
        $dataCheck1 = md5(microtime()) . '1';
        DB::connection($this->connection)
            ->table($this->table)
            ->insert(['firstname' => $dataCheck1, 'lastname' => $dataCheck1]);

        // Insert test data 2.
        $dataCheck2 = md5(microtime()) . '2';
        DB::connection($this->connection)
            ->table($this->table)
            ->insert(['firstname' => $dataCheck2, 'lastname' => $dataCheck2]);

        $modelBuilder = new ModelBuilder($this->connection, $this->table);

        // Set guarded fields to check on.
        $guardedAttributes = [
            $check1 = md5(microtime()),
            $check2 = md5(microtime())
        ];
        $modelBuilder->setGuardedAttributes($guardedAttributes);

        $tokens = $this->callPrivateMethod('getTokens', $modelBuilder);
        $this->assertEquals($check . '\\' . ucfirst($this->connection), $tokens['namespace']);
        $this->assertEquals([\CoRex\Laravel\Model\Model::class], $tokens['uses']);
        $this->assertEquals(4, count($tokens['phpdocProperties']));
        $this->assertEquals(ucfirst($this->table), $tokens['Class']);
        $this->assertEquals('Model', $tokens['extends']);
        $this->assertEquals($this->connection, $tokens['connection']);
        $this->assertEquals($this->table, $tokens['table']);
        $this->assertEquals(4, count($tokens['fillable']));
        $this->assertEquals($guardedAttributes, $tokens['guarded']);
        $keys = array_keys($tokens['constants']);
        $this->assertEquals('C' . strtoupper($dataCheck1), $keys[0]);
        $this->assertEquals('C' . strtoupper($dataCheck2), $keys[1]);
        $this->assertEquals([], $tokens['preservedLines']);
        $this->assertFalse($tokens['timestamps']);
    }

    /**
     * Test get doc properties.
     * @throws Exception
     */
    public function testGetDocProperties()
    {
        $this->createTable();
        $modelBuilder = new ModelBuilder($this->connection, $this->table);
        $docProperties = $this->callPrivateMethod('getDocProperties', $modelBuilder);
        $this->assertTrue(is_int(strpos($docProperties[0], 'integer $id')));
        $this->assertTrue(is_int(strpos($docProperties[1], 'string $firstname')));
        $this->assertTrue(is_int(strpos($docProperties[2], 'string $lastname')));
    }

    /**
     * Test get attributes.
     *
     * @throws Exception
     */
    public function testGetAttributes()
    {
        // Tested through 'getDocProperties'.
        $this->testGetDocProperties();
    }

    /**
     * Test get indent.
     * @throws Exception
     */
    public function testGetIndent()
    {
        $this->createTable();
        $modelBuilder = new ModelBuilder($this->connection, $this->table);

        // Test default (4 spaces).
        $this->assertEquals('    ', $this->callPrivateMethod('getIndent', $modelBuilder));

        // Test random.
        $check = md5(microtime());
        $this->app['config']->set('corex', [
            'lmodel' => [
                'indent' => $check
            ]
        ]);
        $this->assertEquals($check, $this->callPrivateMethod('getIndent', $modelBuilder));
    }

    /**
     * Test get line length.
     * @throws Exception
     */
    public function testGetLinelength()
    {
        $this->createTable();
        $modelBuilder = new ModelBuilder($this->connection, $this->table);

        // Test default (120).
        $this->assertEquals(120, $this->callPrivateMethod('getLinelength', $modelBuilder));

        // Test random.
        $length = mt_rand(80, 120);
        $this->app['config']->set('corex', [
            'lmodel' => [
                'length' => $length
            ]
        ]);
        $this->assertEquals($length, $this->callPrivateMethod('getLinelength', $modelBuilder));
    }

    /**
     * Test remove tag lines.
     * @throws Exception
     */
    public function testRemoveTagLines()
    {
        $this->createTable();
        $modelBuilder = new ModelBuilder($this->connection, $this->table);
        $check = md5(microtime());
        $stub = implode("\r\n", ['', '{{' . $check . '}}', '']) . "\r\n";
        $stub = $this->callPrivateMethod('removeTagLines', $modelBuilder, $stub);
        $this->assertEquals(implode("\n", ['', '', '']) . "\n", $stub);
    }

    /**
     * Test get constants.
     * @throws Exception
     */
    public function testGetConstants()
    {
        // Tested through getTokens().
        $this->testGetTokens();
    }

    /**
     * Setup.
     */
    protected function setUp()
    {
        parent::setUp();

        // Set database configuration.
        $config = require(dirname(__DIR__) . '/database.php');
        $connection = $config['default'];
        $this->app['config']->set('database.default', $connection);
        $this->app['config']->set('database.connections.' . $connection, $config['connections'][$connection]);

        // Remove existing table.
        if ($this->getDoctrineSchemaManager()->tablesExist([$this->table])) {
            $this->getDoctrineSchemaManager()->dropTable($this->table);
        }
    }

    /**
     * Get doctrine schema manager.
     *
     * @return \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    private function getDoctrineSchemaManager()
    {
        return DB::connection($this->connection)->getDoctrineSchemaManager();
    }

    /**
     * Create table.
     */
    private function createTable()
    {
        // Create table.
        require_once(__DIR__ . '/migrations/status_table.php');
        $migration = new StatusTable();
        $migration->up();
    }

    /**
     * Get property.
     *
     * @param string $name
     * @param object $object
     * @return mixed
     * @throws ReflectionException
     */
    private function getProperty($name, $object)
    {
        $reflectionClass = new ReflectionClass(get_class($object));
        $property = $reflectionClass->getProperty($name);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    /**
     * Call private method.
     *
     * @param string $name
     * @param object $object
     * @return mixed
     * @throws ReflectionException
     */
    private function callPrivateMethod($name, $object)
    {
        $arguments = array_slice(func_get_args(), 2);
        $method = new ReflectionMethod(get_class($object), $name);
        $method->setAccessible(true);
        if (count($arguments) > 0) {
            return $method->invokeArgs($object, $arguments);
        } else {
            return $method->invoke($object);
        }
    }
}