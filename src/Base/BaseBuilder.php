<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Base;

use CoRex\Laravel\Model\Constants;
use CoRex\Laravel\Model\Helpers\ModelBuilder;
use CoRex\Laravel\Model\Interfaces\BuilderInterface;
use CoRex\Laravel\Model\Interfaces\ConfigInterface;

abstract class BaseBuilder implements BuilderInterface
{
    /** @var ModelBuilder */
    protected $modelBuilder;

    /** @var ConfigInterface */
    protected $config;

    /** @var string */
    protected $connection;

    /** @var string */
    protected $table;

    /**
     * Set model builder.
     *
     * @param ModelBuilder $modelBuilder
     */
    public function setModelBuilder(ModelBuilder $modelBuilder): void
    {
        $this->modelBuilder = $modelBuilder;
        $this->config = $modelBuilder->getConfig();
        $this->connection = $modelBuilder->getConnection();
        $this->table = $modelBuilder->getTable();
    }

    /**
     * Get indentation.
     *
     * @param int $count
     * @return string
     */
    protected function indent(int $count): string
    {
        $indent = $this->modelBuilder->getConfig()->getIndent();
        if ($indent === null) {
            $indent = Constants::DEFAULT_INDENT;
        }

        return str_repeat($indent, $count);
    }
}
