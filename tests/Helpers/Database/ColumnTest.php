<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Helpers\Database;

use CoRex\Helpers\Obj;
use CoRex\Laravel\Model\Helpers\Database\Column;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class ColumnTest extends TestCase
{
    /** @var string[] */
    private $data;

    /** @var Column */
    private $column;

    /**
     * Test constructor.
     *
     * @throws ReflectionException
     */
    public function testConstructor(): void
    {
        $this->assertSame($this->data, Obj::getProperty('data', $this->column));
    }

    /**
     * Test get name.
     */
    public function testGetName(): void
    {
        $this->assertSame($this->data['name'], $this->column->getName());
    }

    /**
     * Test get type.
     */
    public function testGetType(): void
    {
        $this->assertSame($this->data['type'], $this->column->getType());
    }

    /**
     * Test get comment.
     */
    public function testGetComment(): void
    {
        $this->assertSame($this->data['comment'], $this->column->getComment());
    }

    /**
     * Test get.
     *
     * @throws ReflectionException
     */
    public function testGet(): void
    {
        $this->assertSame('unknown.value', Obj::callMethod('get', $this->column, [
            'key' => 'unknown.key',
            'default' => 'unknown.value'
        ]));
    }

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $randomString = md5((string)random_int(1, 100000));
        $this->data = [
            'name' => $randomString . '1',
            'type' => $randomString . '2',
            'comment' => $randomString . '3'
        ];

        $this->column = new Column($this->data);
    }
}