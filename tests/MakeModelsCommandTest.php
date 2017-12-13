<?php

use CoRex\Laravel\Model\Commands\MakeModelsCommand;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\TestCase;

class MakeModelsCommandTest extends TestCase
{
    private $connection = 'test';
    private $table = 'status';

    /**
     * Test handle.
     */
    public function testHandle()
    {
        // Create basic configuration.
        $path = dirname(__DIR__) . '/temp';
        $this->app['config']->set('corex', [
            'lmodel' => [
                'path' => $path,
                'namespace' => 'App\Models',
                'addConnection' => true,
                'extends' => Model::class,
                'const' => [
                    $this->connection => [
                        $this->table => [
                            'id' => 'id',
                            'name' => 'firstname',
                            'prefix' => 'C'
                        ]
                    ]
                ]
            ]
        ]);
        $this->createTable();

        // Add command.
        $kernel = $this->getConsoleKernel();
        $reflectionClass = new ReflectionClass(get_class($kernel));
        $commands = $reflectionClass->getProperty('commands');
        $commands->setAccessible(true);
        $commands->setValue($kernel, [
            MakeModelsCommand::class
        ]);

        // Call artisan make:models.
        $this->artisan('make:models', [
            'connection' => $this->connection,
            'tables' => $this->table
        ]);

        // Check that model was created.
        $modelFilename = $this->getModelFilename();
        $this->assertFileExists($modelFilename);

        // Get list of fields to check on.
        $tableDetails = $this->getDoctrineSchemaManager()->listTableDetails($this->table);
        $columns = array_keys($tableDetails->getColumns());
        $checkColumnsString = '\'' . implode('\', \'', $columns) . '\'';

        // Check content of model.
        $content = file_get_contents($modelFilename);
        $this->assertTrue(\Illuminate\Support\Str::contains($content, 'public $timestamps'));
        $this->assertTrue(\Illuminate\Support\Str::contains($content, '$connection = \'' . $this->connection . '\''));
        $this->assertTrue(\Illuminate\Support\Str::contains($content, '$table = \'' . $this->table . '\''));
        $this->assertTrue(\Illuminate\Support\Str::contains($content, '$fillable = [' . $checkColumnsString . '];'));
        $this->assertTrue(\Illuminate\Support\Str::contains($content, '$guarded = [];'));
    }

    /**
     * Create table.
     */
    private function createTable()
    {
        // Create table.
        require_once(__DIR__ . '/migrations/status_table.php');
        $migration = new StatusTable();
        $migration->up();
    }

    /**
     * Get console kernel.
     *
     * @return \Illuminate\Contracts\Console\Kernel
     */
    private function getConsoleKernel()
    {
        return $this->app['Illuminate\Contracts\Console\Kernel'];
    }

    /**
     * Get doctrine schema manager.
     *
     * @return \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    private function getDoctrineSchemaManager()
    {
        return DB::connection($this->connection)->getDoctrineSchemaManager();
    }

    /**
     * Get model filename.
     *
     * @return string
     */
    private function getModelFilename()
    {
        return implode("/", [
            dirname(__DIR__) . '/temp',
            ucfirst($this->connection),
            ucfirst($this->table) . '.php'
        ]);
    }

    /**
     * Setup.
     */
    protected function setUp()
    {
        parent::setUp();

        // Set database configuration.
        $config = require(dirname(__DIR__) . '/database.php');
        $connection = $config['default'];
        $this->app['config']->set('database.default', $connection);
        $this->app['config']->set('database.connections.' . $connection, $config['connections'][$connection]);

        // Remove existing table.
        if ($this->getDoctrineSchemaManager()->tablesExist([$this->table])) {
            $this->getDoctrineSchemaManager()->dropTable($this->table);
        }

        // Remove existing file.
        $modelFilename = $this->getModelFilename();
        if (file_exists($modelFilename)) {
            unlink($modelFilename);
        }
    }
}
