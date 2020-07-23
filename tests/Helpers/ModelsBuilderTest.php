<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Helpers;

use CoRex\Helpers\Obj;
use CoRex\Laravel\Model\Exceptions\ConfigException;
use CoRex\Laravel\Model\Exceptions\WriterException;
use CoRex\Laravel\Model\Helpers\Config;
use CoRex\Laravel\Model\Helpers\Database;
use CoRex\Laravel\Model\Helpers\ModelsBuilder;
use CoRex\Laravel\Model\Helpers\Writer;
use Doctrine\DBAL\DBALException;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Container\BindingResolutionException;
use ReflectionException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\CoRex\Laravel\Model\Commands\FakeWriter;
use Tests\CoRex\Laravel\Model\TestBase;

class ModelsBuilderTest extends TestBase
{
    /** @var ModelsBuilder */
    private $modelsBuilder;

    /**
     * Test set config.
     *
     * @throws ReflectionException
     */
    public function testSetConfig(): void
    {
        $config = new Config(['unknown' => 'unknown']);
        $this->assertNull(Obj::getProperty('config', $this->modelsBuilder));
        $this->modelsBuilder->setConfig($config);
        $this->assertSame($config, Obj::getProperty('config', $this->modelsBuilder));
    }

    /**
     * Test set writer.
     *
     * @throws ReflectionException
     */
    public function testSetWriter(): void
    {
        $writer = new Writer();
        $this->assertNull(Obj::getProperty('writer', $this->modelsBuilder));
        $this->modelsBuilder->setWriter($writer);
        $this->assertSame($writer, Obj::getProperty('writer', $this->modelsBuilder));
    }

    /**
     * Test set database.
     *
     * @throws ReflectionException
     */
    public function testSetDatabase(): void
    {
        $database = new Database();
        $this->assertNull(Obj::getProperty('database', $this->modelsBuilder));
        $this->modelsBuilder->setDatabase($database);
        $this->assertSame($database, Obj::getProperty('database', $this->modelsBuilder));
    }

    /**
     * Test set application.
     *
     * @throws ReflectionException
     */
    public function testSetApplication(): void
    {
        $this->assertNull(Obj::getProperty('application', $this->modelsBuilder));
        $this->modelsBuilder->setApplication($this->application);
        $this->assertSame($this->application, Obj::getProperty('application', $this->modelsBuilder));
    }

    /**
     * Test set output.
     *
     * @throws ReflectionException
     */
    public function testSetOutput(): void
    {
        $outputStyle = new OutputStyle(new ArgvInput(), new BufferedOutput());
        $this->assertNull(Obj::getProperty('output', $this->modelsBuilder));
        $this->modelsBuilder->setOutput($outputStyle);
        $this->assertSame($outputStyle, Obj::getProperty('output', $this->modelsBuilder));
    }

    /**
     * Test execute.
     *
     * @throws ConfigException
     * @throws WriterException
     * @throws BindingResolutionException
     */
    public function testExecute(): void
    {
        $this->modelsBuilder->setConfig($this->config);

        $fakeWriter = new FakeWriter();
        $this->modelsBuilder->setWriter($fakeWriter);

        $this->modelsBuilder->setDatabase($this->database);
        $this->modelsBuilder->setApplication($this->application);

        $bufferedOutput = new BufferedOutput();
        $outputStyle = new OutputStyle(new ArgvInput(), $bufferedOutput);
        $this->modelsBuilder->setOutput($outputStyle);

        $this->modelsBuilder->execute(
            [
                'connection' => 'testbench',
                'tables' => '.'
            ],
            []
        );

        $output = $bufferedOutput->fetch();
        $this->assertStringContainsString('Create/update model(s) from existing schema', $output);
        $this->assertStringContainsString(
            'Model [Tests\CoRex\Laravel\Model\Files\Testbench\Lmodel] generated',
            $output
        );
        $this->assertStringContainsString('Table ltest ignored', $output);
        $this->assertStringContainsString('1 model(s) generated', $output);
    }

    /**
     * Test execute with package definition.
     *
     * @throws BindingResolutionException
     * @throws ConfigException
     * @throws ReflectionException
     * @throws WriterException
     */
    public function testExecuteWithPackageDefinition(): void
    {
        $data = $this->config->toArray();
        $data['packages']['my/package']['patterns'] = ['^lmodel'];
        Obj::setProperty('data', $this->config, $data);

        $this->modelsBuilder->setConfig($this->config);
        $this->modelsBuilder->setDatabase($this->database);
        $this->modelsBuilder->setApplication($this->application);

        $bufferedOutput = new BufferedOutput();
        $outputStyle = new OutputStyle(new ArgvInput(), $bufferedOutput);
        $this->modelsBuilder->setOutput($outputStyle);

        $this->modelsBuilder->execute(
            [
                'connection' => 'testbench',
                'tables' => '.'
            ],
            [
                'destination' => false,
                'console' => true
            ]
        );

        $output = $bufferedOutput->fetch();
        $this->assertStringContainsString('CoRex\Laravel\Model\Models', $output);
    }

    /**
     * Test execute option destination.
     *
     * @throws BindingResolutionException
     * @throws ConfigException
     * @throws WriterException
     */
    public function testExecuteDestination(): void
    {
        $this->modelsBuilder->setConfig($this->config);
        $this->modelsBuilder->setDatabase($this->database);
        $this->modelsBuilder->setApplication($this->application);

        $bufferedOutput = new BufferedOutput();
        $outputStyle = new OutputStyle(new ArgvInput(), $bufferedOutput);
        $this->modelsBuilder->setOutput($outputStyle);

        $this->modelsBuilder->execute(
            [
                'connection' => 'testbench',
                'tables' => '.'
            ],
            [
                'destination' => true,
                'console' => false
            ]
        );

        $output = $bufferedOutput->fetch();
        $this->assertStringContainsString('Files\Testbench\Lmodel', $output);
        $this->assertStringContainsString('Files/Testbench/Lmodel.php', $output);
    }

    /**
     * Test execute option console.
     *
     * @throws BindingResolutionException
     * @throws ConfigException
     * @throws WriterException
     */
    public function testExecuteConsole(): void
    {
        $this->modelsBuilder->setConfig($this->config);
        $this->modelsBuilder->setDatabase($this->database);
        $this->modelsBuilder->setApplication($this->application);

        $bufferedOutput = new BufferedOutput();
        $outputStyle = new OutputStyle(new ArgvInput(), $bufferedOutput);
        $this->modelsBuilder->setOutput($outputStyle);

        $this->modelsBuilder->execute(
            [
                'connection' => 'testbench',
                'tables' => '.'
            ],
            [
                'destination' => false,
                'console' => true
            ]
        );

        $output = $bufferedOutput->fetch();
        $this->assertStringContainsString('declare(strict_types=1)', $output);
        $this->assertStringContainsString('class Lmodel extends Model', $output);
        $this->assertStringContainsString('$connection = \'testbench\'', $output);
        $this->assertStringContainsString('$table = \'lmodel\'', $output);
    }

    /**
     * Test register Doctrine mappings.
     *
     * @throws ReflectionException
     * @throws DBALException
     */
    public function testRegisterDoctrineMappings(): void
    {
        $this->modelsBuilder->setConfig($this->config);
        $this->modelsBuilder->setDatabase($this->database);
        Obj::callMethod('registerDoctrineMappings', $this->modelsBuilder);
        $this->assertSame('string', $this->database->getDatabasePlatform()->getDoctrineTypeMapping('test-type'));
    }

    /**
     * Test register custom builders.
     *
     * @throws BindingResolutionException
     * @throws ConfigException
     * @throws WriterException
     */
    public function testRegisterCustomBuilders(): void
    {
        $this->modelsBuilder->setConfig($this->config);

        $fakeWriter = new FakeWriter();
        $this->modelsBuilder->setWriter($fakeWriter);

        $this->modelsBuilder->setDatabase($this->database);
        $this->modelsBuilder->setApplication($this->application);

        $bufferedOutput = new BufferedOutput();
        $outputStyle = new OutputStyle(new ArgvInput(), $bufferedOutput);
        $this->modelsBuilder->setOutput($outputStyle);

        $this->modelsBuilder->execute(
            [
                'connection' => 'testbench',
                'tables' => '.'
            ],
            [
                'destination' => false,
                'console' => true
            ]
        );

        $this->assertStringContainsString('testdeclare', $bufferedOutput->fetch());
    }

    /**
     * Test output.
     *
     * @throws ReflectionException
     */
    public function testOutput(): void
    {
        $bufferedOutput = new BufferedOutput();
        $outputStyle = new OutputStyle(new ArgvInput(), $bufferedOutput);
        $this->assertNull(Obj::getProperty('output', $this->modelsBuilder));
        $this->modelsBuilder->setOutput($outputStyle);
        Obj::callMethod(
            'output',
            $this->modelsBuilder,
            [
                'output' => 'test',
                'style' => 'info'
            ]
        );
        $this->assertSame("test\n", $bufferedOutput->fetch());
    }

    /**
     * Test set connection.
     *
     * @throws ReflectionException
     */
    public function testSetConnection(): void
    {
        $this->modelsBuilder->setApplication($this->application);
        Obj::callMethod(
            'setConnection',
            $this->modelsBuilder,
            [
                'arguments' => [
                    'connection' => 'testbench'
                ]
            ]
        );
        $this->assertSame('testbench', Obj::getProperty('connection', $this->modelsBuilder));
    }

    /**
     * Test set connection default.
     *
     * @throws ReflectionException
     */
    public function testSetConnectionDefault(): void
    {
        $this->modelsBuilder->setApplication($this->application);
        Obj::callMethod(
            'setConnection',
            $this->modelsBuilder,
            [
                'arguments' => [
                    'connection' => '.'
                ]
            ]
        );
        $this->assertSame('testbench', Obj::getProperty('connection', $this->modelsBuilder));
    }

    /**
     * Test set connection exception.
     *
     * @throws ReflectionException
     */
    public function testSetConnectionException(): void
    {
        $this->modelsBuilder->setApplication($this->application);

        $availableConnections = array_keys($this->application->get('config')->get('database.connections'));
        $availableConnectionNames = implode(', ', $availableConnections);

        $message = 'Connection unknown not found. Available connections: ' . $availableConnectionNames . '.';
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage($message);

        Obj::callMethod(
            'setConnection',
            $this->modelsBuilder,
            [
                'arguments' => [
                    'connection' => 'unknown'
                ]
            ]
        );
    }

    /**
     * Test set tables.
     *
     * @throws ReflectionException
     */
    public function testSetTables(): void
    {
        $this->modelsBuilder->setConfig($this->config);
        $this->modelsBuilder->setDatabase($this->database);
        $this->modelsBuilder->setApplication($this->application);

        $bufferedOutput = new BufferedOutput();
        $outputStyle = new OutputStyle(new ArgvInput(), $bufferedOutput);
        $this->modelsBuilder->setOutput($outputStyle);

        Obj::callMethod(
            'setConnection',
            $this->modelsBuilder,
            [
                'arguments' => [
                    'connection' => '.'
                ]
            ]
        );
        Obj::callMethod(
            'setTables',
            $this->modelsBuilder,
            [
                'arguments' => [
                    'connection' => 'testbench',
                    'tables' => '.'
                ]
            ]
        );

        $tables = Obj::getProperty('tables', $this->modelsBuilder);
        $this->assertTrue(in_array('lmodel', $tables, true));
        $this->assertFalse(in_array('ltest', $tables, true));
    }

    /**
     * Test set tables multiple.
     *
     * @throws ReflectionException
     */
    public function testSetTablesMultiple(): void
    {
        $this->modelsBuilder->setConfig($this->config);
        $this->modelsBuilder->setDatabase($this->database);
        $this->modelsBuilder->setApplication($this->application);

        $bufferedOutput = new BufferedOutput();
        $outputStyle = new OutputStyle(new ArgvInput(), $bufferedOutput);
        $this->modelsBuilder->setOutput($outputStyle);

        Obj::callMethod(
            'setConnection',
            $this->modelsBuilder,
            [
                'arguments' => [
                    'connection' => '.'
                ]
            ]
        );
        Obj::callMethod(
            'setTables',
            $this->modelsBuilder,
            [
                'arguments' => [
                    'connection' => 'testbench',
                    'tables' => 'lmodel,ltest'
                ]
            ]
        );

        $tables = Obj::getProperty('tables', $this->modelsBuilder);
        $this->assertTrue(in_array('lmodel', $tables, true));
        $this->assertFalse(in_array('ltest', $tables, true));
    }

    /**
     * Test set tables unknown.
     *
     * @throws ReflectionException
     */
    public function testSetTablesUnknown(): void
    {
        $this->modelsBuilder->setConfig($this->config);
        $this->modelsBuilder->setDatabase($this->database);
        $this->modelsBuilder->setApplication($this->application);

        $bufferedOutput = new BufferedOutput();
        $outputStyle = new OutputStyle(new ArgvInput(), $bufferedOutput);
        $this->modelsBuilder->setOutput($outputStyle);

        Obj::callMethod(
            'setConnection',
            $this->modelsBuilder,
            [
                'arguments' => [
                    'connection' => '.'
                ]
            ]
        );

        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Table unknown not found.');
        Obj::callMethod(
            'setTables',
            $this->modelsBuilder,
            [
                'arguments' => [
                    'connection' => 'testbench',
                    'tables' => 'unknown'
                ]
            ]
        );
    }

    /**
     * Test Laravel config.
     *
     * @throws ReflectionException
     */
    public function testLaravelConfig(): void
    {
        $this->modelsBuilder->setApplication($this->application);

        $this->assertSame(
            $this->application->get('config')->get('database.default'),
            Obj::callMethod('laravelConfig', $this->modelsBuilder, ['key' => 'database.default'])
        );
    }

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->modelsBuilder = new ModelsBuilder();
    }
}
