<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Helpers;

use CoRex\Helpers\Obj;
use CoRex\Laravel\Model\Builders\DeclareStrictBuilder;
use CoRex\Laravel\Model\Constants;
use CoRex\Laravel\Model\Exceptions\ConfigException;
use CoRex\Laravel\Model\Helpers\Config;
use CoRex\Laravel\Model\Helpers\Definitions\PackageDefinitions;
use CoRex\Laravel\Model\Helpers\Definitions\TableDefinition;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\TestCase;
use ReflectionException;
use Tests\CoRex\Laravel\Model\TestData;

class ConfigTest extends TestCase
{
    /**
     * Test constructor.
     *
     * @throws ReflectionException
     */
    public function testConstructor(): void
    {
        $this->assertSame(
            TestData::getConfig(),
            Obj::getProperty('data', $this->config())
        );
    }

    /**
     * Test validate no configuration.
     *
     * @throws ConfigException
     */
    public function testValidateNoConfiguration(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Configuration not specified.');
        (new Config([]))->validate();
    }

    /**
     * Test validate success.
     *
     * @throws ConfigException
     */
    public function testValidateSuccess(): void
    {
        $this->assertTrue($this->config()->validate());
    }

    /**
     * Test validate declareStrict.
     *
     * @throws ConfigException
     */
    public function testValidateDeclareStrict(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Configuration: [declareStrict] not set.');
        $this->config([], ['declareStrict'])->validate();
    }

    /**
     * Test validate path.
     *
     * @throws ConfigException
     */
    public function testValidatePath(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Configuration: [path] not set.');
        $this->config([], ['path'])->validate();
    }

    /**
     * Test validate namespace.
     *
     * @throws ConfigException
     */
    public function testValidateNamespace(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Configuration: [namespace] not set.');
        $this->config([], ['namespace'])->validate();
    }

    /**
     * Test validate add namespace to connection.
     *
     * @throws ConfigException
     */
    public function testValidateAddNamespaceConnection(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Configuration: [addConnectionToNamespace] not set.');
        $this->config([], ['addConnectionToNamespace'])->validate();
    }

    /**
     * Test validate add database connection.
     *
     * @throws ConfigException
     */
    public function testValidateAddDatabaseConnection(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Configuration: [addDatabaseConnection] not set.');
        $this->config([], ['addDatabaseConnection'])->validate();
    }

    /**
     * Test validate add database table.
     *
     * @throws ConfigException
     */
    public function testValidateAddDatabaseTable(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Configuration: [addDatabaseTable] not set.');
        $this->config([], ['addDatabaseTable'])->validate();
    }

    /**
     * Test validate extends.
     *
     * @throws ConfigException
     */
    public function testValidateExtends(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Configuration: [extends] not set.');
        $this->config([], ['extends'])->validate();
    }

    /**
     * Test get declareStrict.
     */
    public function testGetDeclareStrict(): void
    {
        $this->assertTrue($this->config(['declareStrict' => true])->getDeclareStrict());
        $this->assertFalse($this->config(['declareStrict' => false])->getDeclareStrict());
    }

    /**
     * Test get Path.
     */
    public function testGetPath(): void
    {
        $this->assertSame(dirname(__DIR__) . '/Files', $this->config()->getPath());
    }

    /**
     * Test get namespace.
     */
    public function testGetNamespace(): void
    {
        $this->assertSame('Tests\CoRex\Laravel\Model\Files', $this->config()->getNamespace());
    }

    /**
     * Test get add connection to namespace.
     */
    public function testGetAddConnectionToNamespace(): void
    {
        $this->assertTrue($this->config(['addConnectionToNamespace' => true])->getAddConnectionToNamespace());
        $this->assertFalse($this->config(['addConnectionToNamespace' => false])->getAddConnectionToNamespace());
    }

    /**
     * Test get add database connection.
     */
    public function testGetAddDatabaseConnection(): void
    {
        $this->assertTrue($this->config(['addDatabaseConnection' => true])->getAddDatabaseConnection());
        $this->assertFalse($this->config(['addDatabaseConnection' => false])->getAddDatabaseConnection());
    }

    /**
     * Test get add database table.
     */
    public function testGetAddDatabaseTable(): void
    {
        $this->assertTrue($this->config(['addDatabaseTable' => true])->getAddDatabaseTable());
        $this->assertFalse($this->config(['addDatabaseTable' => false])->getAddDatabaseTable());
    }

    /**
     * Test get extends.
     */
    public function testGetExtends(): void
    {
        $this->assertSame(Model::class, $this->config()->getExtends());
    }

    /**
     * Test get indent.
     */
    public function testGetIndent(): void
    {
        $this->assertNull($this->config(['indent' => null])->getIndent());
        $this->assertSame('testing', $this->config(['indent' => 'testing'])->getIndent());
    }

    /**
     * Test get max line length.
     */
    public function testGetMaxLineLength(): void
    {
        $this->assertSame(7, $this->config(['maxLineLength' => 7])->getMaxLineLength());
    }

    /**
     * Test get Doctrine mappings.
     */
    public function testGetDoctrineMappings(): void
    {
        $this->assertSame(['test-type' => 'string'], $this->config()->getDoctrineMappings());
    }

    /**
     * Test get column type mappings.
     */
    public function testGetPhpDocMappings(): void
    {
        // Assert standard.
        $this->assertSame(Constants::STANDARD_COLUMN_MAPPINGS, $this->config()->getPhpDocMappings());

        // Assert standard + config.
        $mappings = array_merge(
            Constants::STANDARD_COLUMN_MAPPINGS,
            ['from' => 'to']
        );
        $this->assertSame(
            $mappings,
            $this->config(
                [
                    'phpdoc' => [
                        'from' => 'to'
                    ]
                ]
            )->getPhpDocMappings()
        );
    }

    /**
     * Test get builder mappings.
     */
    public function testGetBuilderMappings(): void
    {
        // Assert standard.
        $this->assertSame(
            [
                DeclareStrictBuilder::class => FakeDeclareStrictBuilder::class
            ],
            $this->config()->getBuilderMappings()
        );

        // Assert standard + config.
        $this->assertSame(
            [
                'from' => 'to'
            ],
            $this->config(
                [
                    'builders' => [
                        'from' => 'to'
                    ]
                ]
            )->getBuilderMappings()
        );
    }

    /**
     * Test get ignored tables.
     */
    public function testGetIgnoredTables(): void
    {
        $this->assertSame([], $this->config()->getIgnoredTables('unknown'));
        $this->assertSame(['ltest', 'table2'], $this->config()->getIgnoredTables('testbench'));
    }

    /**
     * Test get package definition.
     */
    public function testGetPackageDefinitions(): void
    {
        $packageDefinitions = $this->config()->getPackageDefinitions();
        $this->assertInstanceOf(PackageDefinitions::class, $packageDefinitions);
    }

    /**
     * Test get table definition.
     */
    public function testGetTableDefinition(): void
    {
        $tableDefinition = $this->config()->getTableDefinition('testbench', 'lmodel');
        $this->assertInstanceOf(TableDefinition::class, $tableDefinition);
        $this->assertTrue($tableDefinition->isValid());

        $tableDefinition = $this->config()->getTableDefinition('unknown', 'unknown');
        $this->assertInstanceOf(TableDefinition::class, $tableDefinition);
        $this->assertFalse($tableDefinition->isValid());
    }

    /**
     * Test to array.
     */
    public function testToArray(): void
    {
        $this->assertSame(
            TestData::getConfig(),
            $this->config()->toArray()
        );
    }

    /**
     * Config object.
     *
     * @param mixed[] $change
     * @param string[] $remove
     * @return Config
     */
    private function config(array $change = [], array $remove = []): Config
    {
        $data = TestData::getConfig($change, $remove);

        return new Config($data);
    }
}
