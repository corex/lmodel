<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Builders\PreservedLinesBuilder;
use Tests\CoRex\Laravel\Model\TestBase;

class PreservedLinesBuilderTest extends TestBase
{
    /**
     * Test build contains.
     */
    public function testBuildContains(): void
    {
        $lines = $this->createBuilder(PreservedLinesBuilder::class)->build();
        $this->assertLinesContains($this->indent(1) . 'public function test(): void', $lines);
    }

    /**
     * Test build not contains.
     */
    public function testBuildNotContains(): void
    {
        $this->modelBuilder->getParser()->setFilename('unknown');
        $lines = $this->createBuilder(PreservedLinesBuilder::class)->build();
        $this->assertLinesNotContains('public function test', $lines);
    }
}
