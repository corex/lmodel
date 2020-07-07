<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Base\BaseBuilder;

class DeclareStrictBuilder extends BaseBuilder
{
    /**
     * Build.
     *
     * @return string[]
     */
    public function build(): array
    {
        if ($this->config->getDeclareStrict()) {
            return [
                'declare(strict_types=1);',
                '',
            ];
        }

        return [];
    }
}
