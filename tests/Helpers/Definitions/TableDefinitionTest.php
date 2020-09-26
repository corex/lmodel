<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Helpers\Definitions;

use CoRex\Helpers\Obj;
use CoRex\Laravel\Model\Helpers\Definitions\ConstantDefinition;
use CoRex\Laravel\Model\Helpers\Definitions\TableDefinition;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use Tests\CoRex\Laravel\Model\TestData;

class TableDefinitionTest extends TestCase
{
    /** @var mixed[] */
    private $data;

    /** @var TableDefinition */
    private $definition;

    /**
     * Test constructor.
     *
     * @throws ReflectionException
     */
    public function testConstructor(): void
    {
        $this->assertSame(
            $this->data,
            Obj::getProperty('data', $this->definition)
        );
    }

    /**
     * Test is valid.
     */
    public function testIsValid(): void
    {
        $this->assertFalse((new TableDefinition([]))->isValid());
        $this->assertTrue((new TableDefinition(['something' => 'something']))->isValid());
    }

    /**
     * Test get timestamps created_at.
     */
    public function testGetTimestampsCreatedAt(): void
    {
        $this->assertSame($this->data['created_at'], $this->definition->getTimestampsCreatedAt());
    }

    /**
     * Test get timestamps updated_at.
     */
    public function testGetTimestampsUpdatedAt(): void
    {
        $this->assertSame($this->data['updated_at'], $this->definition->getTimestampsUpdatedAt());
    }

    /**
     * Test get timestamps date_format.
     */
    public function testGetTimestampsDateFormat(): void
    {
        $this->assertSame($this->data['date_format'], $this->definition->getTimestampsDateFormat());
    }

    /**
     * Test get fillable columns.
     */
    public function testGetFillableColumns(): void
    {
        $this->assertSame($this->data['fillable'], $this->definition->getFillableColumns());
    }

    /**
     * Test get guarded columns.
     */
    public function testGetGuardedColumns(): void
    {
        $this->assertSame($this->data['guarded'], $this->definition->getGuardedColumns());
    }

    /**
     * Test get readonly columns.
     */
    public function testGetReadonlyColumns(): void
    {
        $this->assertSame($this->data['readonly'], $this->definition->getReadonlyColumns());
    }

    /**
     * Test get hidden attributes.
     */
    public function testGetHiddenAttributes(): void
    {
        $this->assertSame($this->data['hidden'], $this->definition->getHiddenAttributes());
    }

    /**
     * Test get cast attributes.
     */
    public function testGetCastAttributes(): void
    {
        $this->assertSame($this->data['casts'], $this->definition->getCastAttributes());
    }

    /**
     * Test get accessors.
     */
    public function testGetAccessors(): void
    {
        $this->assertSame($this->data['appends'], $this->definition->getAccessors());
    }

    /**
     * Test get constant definitions.
     *
     * @throws ReflectionException
     */
    public function testGetConstantDefinitions(): void
    {
        $definitions = $this->definition->getConstantDefinitions();
        $this->assertCount(2, $definitions);

        // Assert constants 1.
        $this->assertInstanceOf(ConstantDefinition::class, $definitions[0]);
        $this->assertSame(
            $this->data['constants'][0],
            Obj::getProperty('data', $definitions[0])
        );

        // Assert constants 2.
        $this->assertInstanceOf(ConstantDefinition::class, $definitions[1]);
        $this->assertSame(
            $this->data['constants'][1],
            Obj::getProperty('data', $definitions[1])
        );
    }

    /**
     * Test get.
     *
     * @throws ReflectionException
     */
    public function testGet(): void
    {
        $this->assertSame('unknown', Obj::callMethod('get', $this->definition, ['my.key', 'unknown']));
    }

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->data = TestData::getFullData();
        $this->definition = new TableDefinition($this->data);
    }
}
