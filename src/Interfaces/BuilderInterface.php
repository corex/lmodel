<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Interfaces;

use CoRex\Laravel\Model\Helpers\ModelBuilder;

interface BuilderInterface
{
    /**
     * Set model builder.
     *
     * @param ModelBuilder $modelBuilder
     */
    public function setModelBuilder(ModelBuilder $modelBuilder): void;

    /**
     * Build.
     *
     * @return string[]
     */
    public function build(): array;
}
