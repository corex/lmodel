<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Helpers\Definitions;

use CoRex\Helpers\Obj;
use CoRex\Laravel\Model\Helpers\Definitions\PackageDefinition;
use CoRex\Laravel\Model\Helpers\Definitions\PackageDefinitions;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class PackageDefinitionsTest extends TestCase
{
    /**
     * Test constructor.
     *
     * @throws ReflectionException
     */
    public function testConstructor(): void
    {
        $packages = [
            'my/package' => [
                'package' => dirname(__DIR__, 2),
                'relative' => 'src/Models',
                'patterns' => [
                    '^test'
                ]
            ]
        ];

        $packageDefinitions = new PackageDefinitions($packages);

        $objectPackages = Obj::getProperty('packages', $packageDefinitions);
        foreach ($objectPackages as $objectName => $objectPackage) {
            $this->assertInstanceOf(PackageDefinition::class, $objectPackage);
            $this->assertSame($packages[$objectName], Obj::getProperty('data', $objectPackage));
        }
    }

    /**
     * Test get package match.
     *
     * @throws ReflectionException
     */
    public function testGetPackageMatch(): void
    {
        $packages = [
            'my/package' => [
                'package' => dirname(__DIR__, 2),
                'relative' => 'src/Models',
                'patterns' => [
                    '^test'
                ]
            ]
        ];

        $packageDefinitions = new PackageDefinitions($packages);

        $this->assertNull($packageDefinitions->getPackageMatch('unknown'));

        // Build check package definition.
        $checkPackageDefinition = new PackageDefinition($packages['my/package']);
        $checkPackageDefinitionData = Obj::getProperty('data', $checkPackageDefinition);

        // Get matched package-definition.
        $packageDefinition = $packageDefinitions->getPackageMatch('test');
        $packageDefinitionData = Obj::getProperty('data', $packageDefinition);

        $this->assertSame($checkPackageDefinitionData, $packageDefinitionData);
    }
}
