<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Helpers;

use CoRex\Laravel\Model\Exceptions\WriterException;
use CoRex\Laravel\Model\Helpers\Writer;
use CoRex\Laravel\Model\Interfaces\WriterInterface;
use PHPUnit\Framework\TestCase;

class WriterTest extends TestCase
{
    /** @var WriterInterface */
    private $writer;

    /** @var string */
    private $randomString;

    /**
     * Test set/get filename.
     */
    public function testSetGetFilename(): void
    {
        $this->assertNull($this->writer->getFilename());

        $this->writer->setFilename($this->randomString);
        $this->assertSame($this->randomString, $this->writer->getFilename());
    }

    /**
     * Test clear/set/get content.
     */
    public function testClearSetGetContent(): void
    {
        $this->assertNull($this->writer->getContent());

        $this->writer->setContent($this->randomString);
        $this->assertSame($this->randomString, $this->writer->getContent());

        $this->writer->clearContent();

        $this->assertNull($this->writer->getContent());
    }

    /**
     * Test write with path not created.
     *
     * @throws WriterException
     */
    public function testWritePathNotCreated(): void
    {
        $filename = dirname(__DIR__, 2) . '/temp/' . $this->randomString . '/' . $this->randomString . '.txt';
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('Could not write file "' . $filename . '".');
        $this->writer->setFilename($filename);
        $this->writer->setContent($this->randomString);
        $this->writer->write(false);
    }

    /**
     * Test write with path created.
     *
     * @throws WriterException
     */
    public function testWritePathCreated(): void
    {
        $filename = dirname(__DIR__, 2) . '/temp/' . $this->randomString . '/' . $this->randomString . '.txt';
        $this->writer->setFilename($filename);
        $this->writer->setContent($this->randomString);
        $this->writer->write(true);
        $this->assertFileExists($filename);
        $this->assertSame($this->randomString, file_get_contents($filename));
    }

    /**
     * Test write no content.
     *
     * @throws WriterException
     */
    public function testWriteNoContent(): void
    {
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('No content to write.');
        $this->writer->clearContent();
        $this->writer->write(false);
    }

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->writer = new Writer();
        $this->randomString = md5((string)random_int(1, 100000));
    }
}
