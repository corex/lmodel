<?php

use CoRex\Laravel\Model\Model;
use CoRex\Laravel\Model\ModelParser;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;

class ModelParserTest extends TestCase
{
    private $connection = 'test';
    private $table = 'status';

    /**
     * Test get uses.
     */
    public function testGetUses()
    {
        $filename = $this->getPath() . '/' . $this->getFilename();
        $modelParser = new ModelParser($filename);
        $this->assertEquals([Model::class], $modelParser->getUses());
    }

    /**
     * Test get preserved lines.
     */
    public function testGetPreservedLines()
    {
        $filename = $this->getPath() . '/' . $this->getFilename();
        $modelParser = new ModelParser($filename);
        $preservedLines = $modelParser->getPreservedLines();
        $this->assertTrue(is_array($preservedLines));
        $preservedLines = implode("\n", $preservedLines);
        $this->assertGreaterThan(0, strpos($preservedLines, 'preserveThisFunction'));
    }

    /**
     * Setup.
     */
    protected function setUp()
    {
        parent::setUp();
        $path = $this->getPath();
        $filename = $this->getFilename();
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if (file_exists($path . '/' . $filename)) {
            unlink($path . '/' . $filename);
        }
        copy(__DIR__ . '/Helpers/' . $filename, $path . '/' . $filename);
    }

    /**
     * Get path.
     *
     * @return string
     */
    private function getPath()
    {
        return implode('/', [
            dirname(__DIR__),
            'temp',
            Str::studly($this->connection)
        ]);
    }

    /**
     * Get filename.
     *
     * @return string
     */
    private function getFilename()
    {
        return Str::studly($this->table) . '.php';
    }
}