<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Base\BaseBuilder;

class UsesBuilder extends BaseBuilder
{
    /**
     * Build.
     *
     * @return string[]
     */
    public function build(): array
    {
        $extends = $this->config->getExtends();
        $parser = $this->modelBuilder->getParser();
        $uses = $parser->getUses();

        // Make sure extends class is added to uses.
        if (!in_array($extends, $uses, true)) {
            $uses[] = $extends;
            sort($uses);
        }

        // Build result.
        $result = [];
        foreach ($uses as $use) {
            $result[] = 'use ' . $use . ';';
        }

        if (count($result) > 0) {
            $result[] = '';
        }

        return $result;
    }
}
