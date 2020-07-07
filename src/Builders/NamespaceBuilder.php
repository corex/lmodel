<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Base\BaseBuilder;
use Illuminate\Support\Str;

class NamespaceBuilder extends BaseBuilder
{
    /**
     * Build.
     *
     * @return string[]
     */
    public function build(): array
    {
        $namespace = trim((string)$this->config->getNamespace(), '\\');

        if ($this->config->getAddConnectionToNamespace()) {
            $namespace .= '\\' . Str::studly($this->connection);
        }

        return [
            'namespace ' . $namespace . ';',
            '',
        ];
    }
}
