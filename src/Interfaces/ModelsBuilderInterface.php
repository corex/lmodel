<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Interfaces;

use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Foundation\Application;

interface ModelsBuilderInterface
{
    /**
     * Set config.
     *
     * @param ConfigInterface $config
     */
    public function setConfig(ConfigInterface $config): void;

    /**
     * Set writer.
     *
     * @param WriterInterface $writer
     */
    public function setWriter(WriterInterface $writer): void;

    /**
     * Set database.
     *
     * @param DatabaseInterface $database
     */
    public function setDatabase(DatabaseInterface $database): void;

    /**
     * Set application.
     *
     * @param Application $application
     */
    public function setApplication(Application $application): void;

    /**
     * Set output.
     *
     * @param OutputStyle $output
     */
    public function setOutput(OutputStyle $output): void;

    /**
     * Execute.
     *
     * @param mixed[] $arguments
     * @param mixed[] $options
     */
    public function execute(array $arguments, array $options): void;
}
