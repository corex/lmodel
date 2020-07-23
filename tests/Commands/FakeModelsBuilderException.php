<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Commands;

use CoRex\Laravel\Model\Exceptions\ConfigException;
use CoRex\Laravel\Model\Exceptions\ModelException;
use CoRex\Laravel\Model\Exceptions\WriterException;
use CoRex\Laravel\Model\Helpers\ModelsBuilder;
use Illuminate\Contracts\Container\BindingResolutionException;

class FakeModelsBuilderException extends ModelsBuilder
{
    /**
     * Execute.
     *
     * @param mixed[] $arguments
     * @param array $options
     * @throws BindingResolutionException
     * @throws ConfigException
     * @throws WriterException
     */
    public function execute(array $arguments, array $options): void
    {
        throw new ModelException('Testing exception thrown.');
    }
}
