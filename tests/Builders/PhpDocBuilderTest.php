<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Builders\PhpDocBuilder;
use CoRex\Laravel\Model\Constants;
use Tests\CoRex\Laravel\Model\TestBase;

class PhpDocBuilderTest extends TestBase
{
    /**
     * Test build.
     */
    public function testBuild(): void
    {
        $lines = $this->createBuilder(PhpDocBuilder::class)->build();

        $phpdocLines = ['/**'];
        foreach (Constants::PHPDOC_HEADER as $line) {
            $phpdocLines[] = ' * ' . sprintf($line, $this->connection, $this->table);
        }

        $phpdocLines = array_merge(
            $phpdocLines,
            [
                ' * @property int $id',
                ' * @property-read string $code',
                ' * @property int $number',
                ' * @property string $string',
                ' * @property string $status',
                ' * @property string $created_at_test',
                ' * @property string $updated_at_test',
                ' */',
            ]
        );

        $this->assertSame($phpdocLines, $lines);
    }
}
