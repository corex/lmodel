<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Base\BaseBuilder;

class TraitBuilder extends BaseBuilder
{
    /**
     * Build.
     *
     * @return string[]
     */
    public function build(): array
    {
        $parser = $this->modelBuilder->getParser();
        $traits = $parser->getTraits();

        $result = [];
        foreach ($traits as $trait) {
            $classWithoutNamespace = basename(str_replace('\\', '/', $trait));
            $result[] = $this->indent(1) . 'use ' . $classWithoutNamespace . ';';
        }

        if (count($result) > 0) {
            sort($result);
            $result[] = '';
        }

        return $result;
    }
}
