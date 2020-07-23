<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Base\BaseBuilder;
use CoRex\Laravel\Model\Exceptions\ConfigException;
use Illuminate\Support\Str;

class NamespaceBuilder extends BaseBuilder
{
    /**
     * Build.
     *
     * @return string[]
     * @throws ConfigException
     */
    public function build(): array
    {
        // Get model namespace from package settings.
        $namespace = null;
        if ($this->packageDefinition !== null && $this->packageDefinition->isValid()) {
            $namespace = $this->packageDefinition->getModelNamespace();
        }

        // Get model namespace from config settings.
        if ($namespace === null) {
            $namespace = trim((string)$this->config->getNamespace(), '\\');
            if ($this->config->getAddConnectionToNamespace()) {
                $namespace .= '\\' . Str::studly($this->connection);
            }
        }

        return [
            'namespace ' . $namespace . ';',
            '',
        ];
    }
}
