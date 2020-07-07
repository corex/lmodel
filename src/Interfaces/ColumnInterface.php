<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Interfaces;

interface ColumnInterface
{
    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Get type.
     *
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * Get comment.
     *
     * @return string|null
     */
    public function getComment(): ?string;
}
