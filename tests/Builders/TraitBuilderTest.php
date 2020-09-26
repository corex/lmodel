<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Builders\TraitBuilder;
use Tests\CoRex\Laravel\Model\TestBase;

class TraitBuilderTest extends TestBase
{
    /**
     * Test build existing model.
     */
    public function testBuildExisting(): void
    {
        $lines = $this->createBuilder(TraitBuilder::class)->build();
        $this->assertLinesContains($this->indent(1) . 'use TesterTrait;', $lines);
        $this->assertLinesContains($this->indent(1) . 'use TranslatorTrait;', $lines);
    }

    /**
     * Test build new model.
     */
    public function testBuildNewModel(): void
    {
        $this->modelBuilder->getParser()->setFilename('unknown');

        $lines = $this->createBuilder(TraitBuilder::class)->build();
        $this->assertSame([], $lines);
    }
}
