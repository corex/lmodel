<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Builders\StatementGroupStartBuilder;
use Tests\CoRex\Laravel\Model\TestBase;

class StatementGroupStartBuilderTest extends TestBase
{
    /**
     * Test build.
     */
    public function testBuild(): void
    {
        $lines = $this->createBuilder(StatementGroupStartBuilder::class)->build();
        $this->assertSame(['{'], $lines);
    }
}
