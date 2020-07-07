<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Base\BaseBuilder;

class StatementGroupEndBuilder extends BaseBuilder
{
    /**
     * Build.
     *
     * @return string[]
     */
    public function build(): array
    {
        return [
            '}'
        ];
    }
}
