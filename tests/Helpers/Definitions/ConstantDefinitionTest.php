<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Helpers\Definitions;

use CoRex\Helpers\Obj;
use CoRex\Laravel\Model\Constants;
use CoRex\Laravel\Model\Helpers\Definitions\ConstantDefinition;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Tests\CoRex\Laravel\Model\TestData;

class ConstantDefinitionTest extends TestCase
{
    /**
     * Test constructor.
     *
     * @throws ReflectionException
     */
    public function testConstructor(): void
    {
        $data = TestData::getConstant1Data();
        $this->assertSame(
            $data,
            Obj::getProperty('data', new ConstantDefinition($data))
        );
    }

    /**
     * Test get title.
     */
    public function testGetTitle(): void
    {
        $data = TestData::getConstant1Data();
        $this->assertSame(
            $data['title'],
            (new ConstantDefinition($data))->getTitle()
        );
    }

    /**
     * Test get name column.
     */
    public function testGetNameColumn(): void
    {
        $data = TestData::getConstant1Data();
        $this->assertSame(
            $data['name'],
            (new ConstantDefinition($data))->getNameColumn()
        );
    }

    /**
     * Test get value column.
     */
    public function testGetValueColumn(): void
    {
        $data = TestData::getConstant1Data();
        $this->assertSame(
            $data['value'],
            (new ConstantDefinition($data))->getValueColumn()
        );
    }

    /**
     * Test get name prefix.
     */
    public function testGetNamePrefix(): void
    {
        $data = TestData::getConstant1Data();
        $this->assertSame(
            $data['prefix'],
            (new ConstantDefinition($data))->getNamePrefix()
        );
    }

    /**
     * Test get name suffix.
     */
    public function testGetNameSuffix(): void
    {
        $data = TestData::getConstant1Data();
        $this->assertSame(
            $data['suffix'],
            (new ConstantDefinition($data))->getNameSuffix()
        );
    }

    /**
     * Test get unknown.
     */
    public function testGetUnknown(): void
    {
        $this->assertNull((new ConstantDefinition([]))->getTitle());
    }

    /**
     * Test get name replace.
     */
    public function testGetNameReplace(): void
    {
        $data = TestData::getConstant1Data();
        $this->assertSame(
            array_merge(Constants::STANDARD_REPLACES, $data['replace']),
            (new ConstantDefinition($data))->getNameReplace()
        );
    }
}
