<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Builders\DeclareStrictBuilder;
use Tests\CoRex\Laravel\Model\TestBase;

class DeclareStrictBuilderTest extends TestBase
{
    /**
     * Test build enabled.
     */
    public function testBuildEnabled(): void
    {
        $this->setConfig('declareStrict', true);
        $lines = $this->createBuilder(DeclareStrictBuilder::class)->build();
        $this->assertSame(
            [
                'declare(strict_types=1);',
                '',
            ],
            $lines
        );
    }

    /**
     * Test build disabled.
     */
    public function testBuildDisabled(): void
    {
        $this->setConfig('declareStrict', false);
        $lines = $this->createBuilder(DeclareStrictBuilder::class)->build();
        $this->assertSame([], $lines);
    }
}
