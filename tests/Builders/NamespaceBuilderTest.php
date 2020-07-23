<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Builders\NamespaceBuilder;
use CoRex\Laravel\Model\Helpers\Definitions\PackageDefinition;
use Tests\CoRex\Laravel\Model\TestBase;

class NamespaceBuilderTest extends TestBase
{
    /**
     * Test build with namespace added.
     */
    public function testBuildWithNamespaceAdded(): void
    {
        $lines = $this->createBuilder(NamespaceBuilder::class)->build();

        $this->assertSame(
            [
                'namespace ' . $this->config->getNamespace() . '\\Testbench;',
                '',
            ],
            $lines
        );
    }

    /**
     * Test build without namespace added.
     */
    public function testBuildWithoutNamespaceAdded(): void
    {
        $this->setConfig('addConnectionToNamespace', false);

        $lines = $this->createBuilder(NamespaceBuilder::class)->build();

        $this->assertSame(
            [
                'namespace ' . $this->config->getNamespace() . ';',
                '',
            ],
            $lines
        );
    }

    /**
     * Test build with package namespace.
     */
    public function testBuildWithPackageNamespace(): void
    {
        $packages = $this->getConfig('packages', []);
        $packageDefinitionData = $packages['my/package'];
        $packageDefinition = new PackageDefinition($packageDefinitionData);

        $modelBuilder = $this->modelBuilder;
        $modelBuilder->setPackageDefinition($packageDefinition);
        $content = $modelBuilder->build();

        $this->assertStringContainsString('namespace CoRex\Laravel\Model\Models', $content);
    }
}
