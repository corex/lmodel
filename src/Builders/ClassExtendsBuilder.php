<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Base\BaseBuilder;

class ClassExtendsBuilder extends BaseBuilder
{
    /**
     * Build.
     *
     * @return string[]
     */
    public function build(): array
    {
        $extends = $this->config->getExtends();

        $extends = basename(str_replace('\\', '/', $extends));

        return [
            'class ' . $this->modelBuilder->getClass() . ' extends ' . $extends
        ];
    }
}
