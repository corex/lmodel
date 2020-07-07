<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Commands;

use CoRex\Laravel\Model\Exceptions\WriterException;
use CoRex\Laravel\Model\Helpers\Writer;

class FakeWriter extends Writer
{
    /**
     * Write.
     *
     * @param bool $createPathRecursively
     * @throws WriterException
     */
    public function write(bool $createPathRecursively): void
    {
        // Do nothing.
    }
}
