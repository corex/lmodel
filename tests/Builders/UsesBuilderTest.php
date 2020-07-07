<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Builders\UsesBuilder;
use Tests\CoRex\Laravel\Model\TestBase;

class UsesBuilderTest extends TestBase
{
    /**
     * Test build existing model.
     */
    public function testBuildExistingModel(): void
    {
        $lines = $this->createBuilder(UsesBuilder::class)->build();
        $this->assertSame(
            [
                'use Illuminate\Database\Eloquent\Model;',
                'use Symfony\Component\Console\Tester\TesterTrait;',
                'use Symfony\Contracts\Translation\TranslatorTrait;',
                '',
            ],
            $lines
        );
    }

    /**
     * Test build new model.
     */
    public function testBuildNewModel(): void
    {
        $this->modelBuilder->getParser()->setFilename('unknown');

        $lines = $this->createBuilder(UsesBuilder::class)->build();
        $this->assertSame(
            [
                'use Illuminate\Database\Eloquent\Model;',
                '',
            ],
            $lines
        );
    }
}
