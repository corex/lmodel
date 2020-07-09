<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model;

use CoRex\Helpers\Obj;
use CoRex\Laravel\Model\Interfaces\ConfigInterface;
use CoRex\Laravel\Model\Interfaces\DatabaseInterface;
use CoRex\Laravel\Model\Interfaces\WriterInterface;
use CoRex\Laravel\Model\ModelServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase;

class ModelServiceProviderTest extends TestCase
{
    /**
     * Test boot.
     */
    public function testBoot(): void
    {
        $serviceProvider = new ModelServiceProvider($this->app);
        $serviceProvider->boot();

        $publishes = Obj::getProperty('publishes', $serviceProvider, []);

        $this->assertArrayHasKey(ModelServiceProvider::class, $publishes);

        $rootPath = dirname(__DIR__);

        $key = key($publishes[ModelServiceProvider::class]);
        $key = str_replace($rootPath, '', $key);
        $this->assertSame('/config/lmodel.php', $key);

        $value = current($publishes[ModelServiceProvider::class]);
        $value = str_replace($rootPath, '', $value);
        $this->assertStringContainsString('/config/lmodel.php', $value);
    }

    /**
     * Test register.
     */
    public function testRegister(): void
    {
        $this->assertFalse($this->app->has(ConfigInterface::class));
        $this->assertFalse($this->app->has(WriterInterface::class));
        $this->assertFalse($this->app->has(DatabaseInterface::class));

        $serviceProvider = new ModelServiceProvider($this->app);

        // Register service provider.
        $serviceProvider->register();

        // Assert that command "make:models" has been registered.
        $this->assertTrue(in_array('make:models', array_keys(Artisan::all()), true));

        $this->assertTrue($this->app->has(ConfigInterface::class));
        $this->assertTrue($this->app->has(WriterInterface::class));
        $this->assertTrue($this->app->has(DatabaseInterface::class));
    }
}
