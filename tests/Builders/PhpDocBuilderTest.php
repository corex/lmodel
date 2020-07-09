<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Builders\PhpDocBuilder;
use Tests\CoRex\Laravel\Model\TestBase;

class PhpDocBuilderTest extends TestBase
{
    /**
     * Test build.
     */
    public function testBuild(): void
    {
        $lines = $this->createBuilder(PhpDocBuilder::class)->build();

        $this->assertSame(
            [
                '/**',
                ' * @property int $id',
                ' * @property-read string $code',
                ' * @property int $number',
                ' * @property string $string',
                ' * @property string $status',
                ' * @property string $created_at_test',
                ' * @property string $updated_at_test',
                ' */',
            ],
            $lines
        );
    }
}
