<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Base\BaseBuilder;
use CoRex\Laravel\Model\Constants;

class PreservedLinesBuilder extends BaseBuilder
{
    /**
     * Build.
     *
     * @return string[]
     */
    public function build(): array
    {
        $preservedLines = $this->modelBuilder->getParser()->getPreservedLines();

        return array_merge(
            [$this->indent(1) . Constants::PRESERVED_IDENTIFIER],
            $preservedLines
        );
    }
}
