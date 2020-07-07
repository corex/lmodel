<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Interfaces;

interface ParserInterface
{
    /**
     * Set filename.
     *
     * @param string $filename
     */
    public function setFilename(string $filename): void;

    /**
     * Get uses.
     *
     * @return string[]
     */
    public function getUses(): array;

    /**
     * Get traits.
     *
     * @return mixed[]
     */
    public function getTraits(): array;

    /**
     * Get preserved lines.
     *
     * @return string[]
     */
    public function getPreservedLines(): array;
}
