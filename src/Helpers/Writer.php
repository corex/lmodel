<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Helpers;

use CoRex\Laravel\Model\Exceptions\WriterException;
use CoRex\Laravel\Model\Interfaces\WriterInterface;
use Exception;

class Writer implements WriterInterface
{
    /** @var string|null */
    private $filename;

    /** @var string|null */
    private $content;

    /**
     * Set filename.
     *
     * @param string $filename
     */
    public function setFilename(?string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * Get filename.
     *
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Clear content.
     */
    public function clearContent(): void
    {
        $this->content = null;
    }

    /**
     * Set content.
     *
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * Get content.
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Write.
     *
     * @param bool $createPathRecursively
     * @throws WriterException
     */
    public function write(bool $createPathRecursively): void
    {
        if ($this->content === null) {
            throw new WriterException('No content to write.');
        }

        // Make sure path exists recursively.
        if ($createPathRecursively) {
            $path = dirname($this->filename);
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        try {
            file_put_contents($this->filename, $this->content);
        } catch (Exception $exception) {
            throw new WriterException('Could not write file "' . $this->filename . '".');
        }
    }
}
