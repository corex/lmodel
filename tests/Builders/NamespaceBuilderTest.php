<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Builders\NamespaceBuilder;
use Tests\CoRex\Laravel\Model\TestBase;

class NamespaceBuilderTest extends TestBase
{
    /**
     * Test build with namespace added.
     */
    public function testBuildWithNamespaceAdded(): void
    {
        $lines = $this->createBuilder(NamespaceBuilder::class)->build();

        $this->assertSame([
            'namespace ' . $this->config->getNamespace() . '\\Testbench;',
            '',
        ], $lines);
    }

    /**
     * Test build without namespace added.
     */
    public function testBuildWithoutNamespaceAdded(): void
    {
        $this->setConfig('addConnectionToNamespace', false);

        $lines = $this->createBuilder(NamespaceBuilder::class)->build();

        $this->assertSame([
            'namespace ' . $this->config->getNamespace() . ';',
            '',
        ], $lines);
    }
}
