<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Commands;

use CoRex\Helpers\Obj;
use CoRex\Laravel\Model\Commands\MakeModelsCommand;
use CoRex\Laravel\Model\Exceptions\ModelException;
use CoRex\Laravel\Model\Helpers\Config;
use CoRex\Laravel\Model\Helpers\Database;
use CoRex\Laravel\Model\Helpers\ModelsBuilder;
use CoRex\Laravel\Model\Helpers\Writer;
use CoRex\Laravel\Model\Interfaces\ModelsBuilderInterface;
use Illuminate\Console\Application;
use Illuminate\Support\Facades\Artisan;
use ReflectionException;
use Tests\CoRex\Laravel\Model\TestBase;

class MakeModelsCommandTest extends TestBase
{
    /**
     * Test handle.
     *
     * @throws ReflectionException
     */
    public function testHandle(): void
    {
        // Get test config.
        $configData = require dirname(__DIR__) . '/Files/lmodel-config.php';

        // Create command.
        $config = new Config($configData);
        $writer = new FakeWriter();
        $database = new Database();
        $command = new MakeModelsCommand($config, $writer, $database);

        // Add command.
        Application::starting(
            function (Application $application) use ($command): void {
                $application->add($command);
            }
        );

        // Setup fake models builder.
        $fakeModelsBuilder = new FakeModelsBuilder();
        $this->app->instance(ModelsBuilderInterface::class, $fakeModelsBuilder);

        // Call command.
        Artisan::call(
            'make:models',
            [
                'connection' => 'testbench',
                'tables' => 'ltest',
                '--destination' => true,
                '--console' => true,
            ]
        );

        // Validate config.
        $configCheck = Obj::getProperty('config', $fakeModelsBuilder, null, ModelsBuilder::class);
        $this->assertInstanceOf(Config::class, $configCheck);
        $this->assertSame($config, $configCheck);

        // Validate writer.
        $writerCheck = Obj::getProperty('writer', $fakeModelsBuilder, null, ModelsBuilder::class);
        $this->assertInstanceOf(Writer::class, $writerCheck);
        $this->assertSame($writer, $writerCheck);

        // Validate database.
        $databaseCheck = Obj::getProperty('database', $fakeModelsBuilder, null, ModelsBuilder::class);
        $this->assertInstanceOf(Database::class, $databaseCheck);
        $this->assertSame($database, $databaseCheck);

        // Validate arguments.
        $arguments = $fakeModelsBuilder->getArguments();
        $this->assertTrue(array_key_exists('command', $arguments));
        $this->assertTrue(in_array('make:models', $arguments, true));
        $this->assertTrue(array_key_exists('connection', $arguments));
        $this->assertTrue(in_array('testbench', $arguments, true));
        $this->assertTrue(array_key_exists('tables', $arguments));
        $this->assertTrue(in_array('ltest', $arguments, true));

        // Validate options.
        $options = $fakeModelsBuilder->getOptions();

        $this->assertArrayHasKey('destination', $options);
        $this->assertTrue($options['destination']);

        $this->assertArrayHasKey('console', $options);
        $this->assertTrue($options['console']);
    }

    /**
     * Test handle with exception.
     */
    public function testHandleException(): void
    {
        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('Testing exception thrown.');

        // Get test config.
        $configData = require dirname(__DIR__) . '/Files/lmodel-config.php';

        // Create command.
        $config = new Config($configData);
        $writer = new FakeWriter();
        $database = new Database();
        $command = new MakeModelsCommand($config, $writer, $database);

        // Add command.
        Application::starting(
            function (Application $application) use ($command): void {
                $application->add($command);
            }
        );

        // Setup fake models builder.
        $fakeModelsBuilder = new FakeModelsBuilderException();
        $this->app->instance(ModelsBuilderInterface::class, $fakeModelsBuilder);

        // Call command.
        Artisan::call(
            'make:models',
            [
                'connection' => 'testbench',
                'tables' => 'ltest',
                '--console' => true,
            ]
        );
    }
}
