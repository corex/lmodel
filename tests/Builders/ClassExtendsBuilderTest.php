<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Builders\ClassExtendsBuilder;
use Tests\CoRex\Laravel\Model\TestBase;

class ClassExtendsBuilderTest extends TestBase
{
    /**
     * Test build.
     */
    public function testBuild(): void
    {
        $lines = $this->createBuilder(ClassExtendsBuilder::class)->build();
        $this->assertSame(['class Lmodel extends Model'], $lines);
    }
}
