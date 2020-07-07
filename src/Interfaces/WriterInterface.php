<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Interfaces;

use CoRex\Laravel\Model\Exceptions\WriterException;

interface WriterInterface
{
    /**
     * Set filename.
     *
     * @param string $filename
     */
    public function setFilename(?string $filename): void;

    /**
     * Get filename.
     *
     * @return string|null
     */
    public function getFilename(): ?string;

    /**
     * Clear content.
     */
    public function clearContent(): void;

    /**
     * Set content.
     *
     * @param string $content
     */
    public function setContent(string $content): void;

    /**
     * Get content.
     *
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * Write.
     *
     * @param bool $createPathRecursively
     * @throws WriterException
     */
    public function write(bool $createPathRecursively): void;
}
