<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Builders\StatementGroupEndBuilder;
use Tests\CoRex\Laravel\Model\TestBase;

class StatementGroupEndBuilderTest extends TestBase
{
    /**
     * Test build.
     */
    public function testBuild(): void
    {
        $lines = $this->createBuilder(StatementGroupEndBuilder::class)->build();
        $this->assertSame(['}'], $lines);
    }
}
