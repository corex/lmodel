<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Commands;

use CoRex\Laravel\Model\Exceptions\ConfigException;
use CoRex\Laravel\Model\Exceptions\WriterException;
use CoRex\Laravel\Model\Helpers\ModelsBuilder;
use Illuminate\Contracts\Container\BindingResolutionException;

class FakeModelsBuilder extends ModelsBuilder
{
    /** @var mixed[] */
    private $arguments = [];

    /** @var mixed[] */
    private $options = [];

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
        $this->arguments = $arguments;
        $this->options = $options;
    }

    /**
     * Get arguments.
     *
     * @return mixed[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Get options.
     *
     * @return mixed[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
