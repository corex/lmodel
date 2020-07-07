<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Commands;

use CoRex\Laravel\Model\Helpers\ModelsBuilder;
use CoRex\Laravel\Model\Interfaces\ConfigInterface;
use CoRex\Laravel\Model\Interfaces\DatabaseInterface;
use CoRex\Laravel\Model\Interfaces\ModelsBuilderInterface;
use CoRex\Laravel\Model\Interfaces\WriterInterface;
use Illuminate\Console\Command;

class MakeModelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:models
        {connection : Name of connection (Specify "." for default)}
        {tables : Comma separated table names to generate (Specify "." to generate all)}
        {--dryrun : Do not write model(s), but output content of model(s)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create/update model(s) from existing schema';

    /** @var ConfigInterface */
    private $config;

    /** @var WriterInterface */
    private $writer;

    /** @var DatabaseInterface */
    private $database;

    /**
     * MakeModelsCommand.
     *
     * @param ConfigInterface $config
     * @param WriterInterface $writer
     * @param DatabaseInterface $database
     */
    public function __construct(ConfigInterface $config, WriterInterface $writer, DatabaseInterface $database)
    {
        parent::__construct();

        $this->config = $config;
        $this->writer = $writer;
        $this->database = $database;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $dryrun = $this->option('dryrun');
        $application = $this->getLaravel();

        // Setup and execute models builder.
        $application->bindIf(ModelsBuilderInterface::class, ModelsBuilder::class);
        $modelsBulder = $application->make(ModelsBuilderInterface::class);
        $modelsBulder->setConfig($this->config);
        $modelsBulder->setWriter($this->writer);
        $modelsBulder->setDatabase($this->database);
        $modelsBulder->setApplication($application);
        $modelsBulder->setOutput($this->getOutput());
        $modelsBulder->execute($this->arguments(), $dryrun);
    }
}
