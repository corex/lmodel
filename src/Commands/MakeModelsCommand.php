<?php

namespace CoRex\Laravel\Model\Commands;

use CoRex\Laravel\Model\Config;
use CoRex\Laravel\Model\ModelBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MakeModelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:models
        {connection : Name of connection (see config/database.php).}
        {tables : Comma separated table names to generate. Specify "." to generate all.}
        {--guarded= : Comma separated list of guarded fields.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create model(s) from existing schema';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Config::validate();
        $connection = $this->getConnection();
        $tables = $this->getTables($connection);
        $guardedAttributes = $this->getGuardedAttributes();

        // Make models.
        if (count($tables) > 0) {
            foreach ($tables as $table) {
                $modelBuilder = new ModelBuilder($connection, $table);
                $modelBuilder->setGuardedAttributes($guardedAttributes);
                $filename = $modelBuilder->getModelFilename();
                $content = $modelBuilder->generateModel();
                $this->writeFile($filename, $content);
                $this->info('Model [' . $filename . '] created.');
            }
        }
    }

    /**
     * Write file.
     *
     * @param string $filename
     * @param string $content
     */
    private function writeFile($filename, $content)
    {
        $permissions = fileperms(base_path('app'));
        $path = dirname($filename);
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, $permissions, true);
        }
        File::put($filename, $content);
    }

    /**
     * Get connection.
     *
     * @return array|string
     * @throws \Exception
     */
    private function getConnection()
    {
        $connections = array_keys(config('database.connections'));
        $connection = $this->argument('connection');
        if ($connection == '.') {
            $connection = config('database.default');
        }
        if (!in_array($connection, $connections)) {
            $message = 'Connection ' . $connection . ' not found.';
            $message .= ' Available connections: ' . implode(', ', $connections) . '.';
            throw new \Exception($message);
        }
        return $connection;
    }

    /**
     * Get tables.
     *
     * @param string $connection
     * @return array
     * @throws \Exception
     */
    private function getTables($connection)
    {
        $existingTables = DB::connection($connection)->getDoctrineSchemaManager()->listTableNames();
        $tables = $this->argument('tables');
        if ($tables != '.') {
            $tables = explode(',', $tables);
            if (count($tables) > 0) {
                foreach ($tables as $table) {
                    if (!in_array($table, $existingTables)) {
                        throw new \Exception('Table ' . $table . ' not found.');
                    }
                }
            }
        } else {
            $tables = $existingTables;
        }
        return $tables;
    }

    /**
     * Get guarded attributes.
     *
     * @return array
     */
    private function getGuardedAttributes()
    {
        $guardedAttributes = $this->option('guarded');
        if ($guardedAttributes !== null && $guardedAttributes != '') {
            return str_replace(' ', '', explode(',', $guardedAttributes));
        }
        return [];
    }
}
