<?php

namespace CoRex\Laravel\Model;

use CoRex\Laravel\Model\Commands\MakeModelsCommand;
use Illuminate\Support\ServiceProvider;

class ModelServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->commands(MakeModelsCommand::class);
    }
}
