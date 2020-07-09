<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model;

use CoRex\Laravel\Model\Commands\MakeModelsCommand;
use CoRex\Laravel\Model\Helpers\Config;
use CoRex\Laravel\Model\Helpers\Database;
use CoRex\Laravel\Model\Helpers\Writer;
use CoRex\Laravel\Model\Interfaces\ConfigInterface;
use CoRex\Laravel\Model\Interfaces\DatabaseInterface;
use CoRex\Laravel\Model\Interfaces\WriterInterface;
use Illuminate\Support\ServiceProvider;

class ModelServiceProvider extends ServiceProvider
{
    /**
     * Boot.
     */
    public function boot(): void
    {
        $this->publishes(
            [
                dirname(__DIR__) . '/config/lmodel.php' => config_path('lmodel.php')
            ],
            'lmodel-config'
        );
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $config = config('lmodel', []);

        // Setup binding and instance of Config.
        $this->app->singletonIf(ConfigInterface::class, Config::class);
        $this->app->when(Config::class)
            ->needs('$data')
            ->give($config);

        // Setup binding of writer.
        $this->app->singletonIf(WriterInterface::class, Writer::class);

        // Setup binding of database.
        $this->app->singletonIf(DatabaseInterface::class, Database::class);

        $this->commands([MakeModelsCommand::class]);
    }
}
