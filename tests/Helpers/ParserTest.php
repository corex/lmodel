<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Helpers;

use CoRex\Helpers\Obj;
use CoRex\Laravel\Model\Exceptions\ModelException;
use CoRex\Laravel\Model\Helpers\Parser;
use Orchestra\Testbench\TestCase;
use ReflectionException;

class ParserTest extends TestCase
{
    /** @var string */
    private $filename;

    /**
     * Test setFilename().
     *
     * @throws ModelException
     * @throws ReflectionException
     */
    public function testSetFilename(): void
    {
        $parser = $this->parser(true);
        $this->assertSame($this->filename, Obj::getProperty('filename', $parser));
    }

    /**
     * Test setFilename with no namespace.
     *
     * @throws ModelException
     * @throws ReflectionException
     */
    public function testSetFilenameNoNamespace(): void
    {
        $filename = dirname(__DIR__) . '/Files/LmodelNoNamespace.php';
        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('Not possible to extract namespace from "' . $filename . '".');
        (new Parser())->setFilename($filename);
    }

    /**
     * Test setFilename with no class.
     *
     * @throws ModelException
     * @throws ReflectionException
     */
    public function testSetFilenameNoClass(): void
    {
        $filename = dirname(__DIR__) . '/Files/LmodelNoClass.php';
        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('Not possible to extract class from "' . $filename . '".');
        (new Parser())->setFilename($filename);
    }

    /**
     * Test setFilename with class not found.
     *
     * @throws ModelException
     * @throws ReflectionException
     */
    public function testSetFilenameClassDoesNotFound(): void
    {
        $filename = dirname(__DIR__) . '/Files/LmodelClassNotFound.php';
        $this->expectException(ModelException::class);
        $this->expectExceptionMessage('Class "Tests\CoRex\Laravel\Model\Files\LmodelCustom" does not exist.');
        (new Parser())->setFilename($filename);
    }

    /**
     * Test getUses().
     *
     * @throws ModelException
     * @throws ReflectionException
     */
    public function testGetUses(): void
    {
        $this->assertSame([], $this->parser(false)->getUses());

        $this->assertSame(
            [
                'Illuminate\Database\Eloquent\Model',
                'Symfony\Component\Console\Tester\TesterTrait',
                'Symfony\Contracts\Translation\TranslatorTrait'
            ],
            $this->parser(true)->getUses()
        );
    }

    /**
     * Test getTraits().
     *
     * @throws ModelException
     * @throws ReflectionException
     */
    public function testGetTraits(): void
    {
        $this->assertSame([], $this->parser(false)->getTraits());

        $this->assertSame(
            [
                'Symfony\Component\Console\Tester\TesterTrait',
                'Symfony\Contracts\Translation\TranslatorTrait'
            ],
            $this->parser(true)->getTraits()
        );
    }

    /**
     * Test getPreservedLines().
     *
     * @throws ModelException
     * @throws ReflectionException
     */
    public function testGetPreservedLines(): void
    {
        $this->assertSame([], $this->parser(false)->getPreservedLines());

        $checkLines = [
            '',
            '    /**',
            '     * Test.',
            '     */',
            '    public function test(): void',
            '    {',
            '    }',
        ];

        $this->assertSame($checkLines, $this->parser(true)->getPreservedLines());
    }

    /**
     * Parser.
     *
     * @param bool $setFilename
     * @return Parser
     * @throws ModelException
     * @throws ReflectionException
     */
    private function parser(bool $setFilename): Parser
    {
        $this->filename = dirname(__DIR__) . '/Files/Lmodel.php';

        $filename = $setFilename ? $this->filename : '';

        $parser = new Parser();
        $parser->setFilename($filename);

        return $parser;
    }
}
