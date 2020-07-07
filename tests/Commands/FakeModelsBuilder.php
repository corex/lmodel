<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Commands;

use CoRex\Laravel\Model\Helpers\ModelsBuilder;

class FakeModelsBuilder extends ModelsBuilder
{
    /** @var mixed[] */
    private $arguments = [];

    /** @var bool */
    private $dryrun = false;

    /**
     * Execute.
     *
     * @param mixed[] $arguments
     * @param bool $dryrun
     */
    public function execute(array $arguments, bool $dryrun): void
    {
        $this->arguments = $arguments;
        $this->dryrun = $dryrun;
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
     * Get dryrun.
     *
     * @return bool
     */
    public function getDryrun(): bool
    {
        return $this->dryrun;
    }
}
