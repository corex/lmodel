<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Builders;

use CoRex\Helpers\Obj;
use CoRex\Laravel\Model\Builders\ConstantsBuilder;
use CoRex\Laravel\Model\Helpers\Definitions\ConstantDefinition;
use ReflectionException;
use Tests\CoRex\Laravel\Model\TestBase;

class ConstantsBuilderTest extends TestBase
{
    /**
     * Test build two.
     */
    public function testBuildTwo(): void
    {
        $lines = $this->createBuilder(ConstantsBuilder::class)->build();
        for ($counter = 1; $counter <= 10; $counter++) {
            $this->assertLinesContains('public const NUMCODE_' . $counter . 'S = ' . $counter . ';', $lines);
            $this->assertLinesContains('public const STRCODE_' . $counter . 'S = \'String ' . $counter . '\';', $lines);
        }
    }

    /**
     * Test build one.
     */
    public function testBuildOne(): void
    {
        // Convert to single and not list.
        $configTables = $this->getConfig('tables', []);
        $configTables['testbench']['lmodel']['constants'] = $configTables['testbench']['lmodel']['constants'][1];
        $this->setConfig('tables', $configTables);

        $lines = $this->createBuilder(ConstantsBuilder::class)->build();
        for ($counter = 1; $counter <= 10; $counter++) {
            $this->assertLinesNotContains('public const NUMCODE_' . $counter . 'S = ' . $counter . ';', $lines);
            $this->assertLinesContains('public const STRCODE_' . $counter . 'S = \'String ' . $counter . '\';', $lines);
        }
    }

    /**
     * Test build none.
     *
     * @throws ReflectionException
     */
    public function testBuildNone(): void
    {
        $this->setConfig('tables', []);

        $lines = $this->createBuilder(ConstantsBuilder::class)->build();
        $this->assertSame([], $lines);
    }

    /**
     * Test if string in rows.
     *
     * @throws ReflectionException
     */
    public function testIfStringInRows(): void
    {
        $builder = $this->createBuilder(ConstantsBuilder::class);
        $this->assertFalse(
            Obj::callMethod(
                'ifStringInRows',
                $builder,
                [
                    'rows' => [],
                ]
            )
        );
    }

    /**
     * Test build constants.
     *
     * @throws ReflectionException
     */
    public function testBuildConstants(): void
    {
        $builder = $this->createBuilder(ConstantsBuilder::class);

        $definition = new ConstantDefinition([]);
        $this->assertNull($definition->getNameColumn());
        $this->assertNull($definition->getValueColumn());

        $result = Obj::callMethod(
            'buildConstants',
            $builder,
            [
                'definition' => $definition,
            ]
        );
        $this->assertSame([], $result);
    }
}
