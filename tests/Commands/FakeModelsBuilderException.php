<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Commands;

use CoRex\Laravel\Model\Exceptions\ModelException;
use CoRex\Laravel\Model\Helpers\ModelsBuilder;

class FakeModelsBuilderException extends ModelsBuilder
{
    /**
     * Execute.
     *
     * @param mixed[] $arguments
     * @param bool $dryrun
     * @throws ModelException
     */
    public function execute(array $arguments, bool $dryrun): void
    {
        throw new ModelException('Testing exception thrown.');
    }
}
