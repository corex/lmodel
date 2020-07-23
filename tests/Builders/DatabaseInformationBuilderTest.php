<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Builders;

use CoRex\Helpers\Obj;
use CoRex\Laravel\Model\Builders\DatabaseInformationBuilder;
use CoRex\Laravel\Model\Helpers\Definitions\PackageDefinition;
use ReflectionException;
use Tests\CoRex\Laravel\Model\TestBase;

class DatabaseInformationBuilderTest extends TestBase
{
    private const DATABASE = '// Database.';

    private const CONNECTION = '$connection = \'testbench\'';

    private const TABLE = '$table = \'lmodel\'';

    private const FILLABLE = '$fillable = [\'code\', \'number\', \'string\']';

    private const GUARDED = '$guarded = [\'code\', \'number\', \'string\']';

    /**
     * Test build with no settings.
     *
     * @throws ReflectionException
     */
    public function testBuildWithNoSettings(): void
    {
        $this->setConfig('addDatabaseConnection', false);
        $this->setConfig('addDatabaseTable', false);
        $this->setConfig('tables', []);

        $lines = $this->createBuilder(DatabaseInformationBuilder::class)->build();
        $this->assertSame([], $lines);
    }

    /**
     * Test build with connection.
     */
    public function testBuildWithConnection(): void
    {
        $this->setConfig('addDatabaseConnection', true);
        $this->setConfig('addDatabaseTable', false);
        $this->setConfig('tables', []);

        $lines = $this->createBuilder(DatabaseInformationBuilder::class)->build();
        $this->assertLinesContains(self::DATABASE, $lines);
        $this->assertLinesContains(self::CONNECTION, $lines);
        $this->assertLinesNotContains(self::TABLE, $lines);
        $this->assertLinesNotContains(self::FILLABLE, $lines);
        $this->assertLinesNotContains(self::GUARDED, $lines);
    }

    /**
     * Test build with connection and package definition.
     *
     * @throws ReflectionException
     */
    public function testBuildWithConnectionAndPackageDefinition(): void
    {
        $this->setConfig('addDatabaseConnection', true);
        $this->setConfig('addDatabaseTable', false);
        $this->setConfig('tables', []);

        $this->modelBuilder->setPackageDefinition(new PackageDefinition([]));
        $builder = new DatabaseInformationBuilder();
        $builder->setModelBuilder($this->modelBuilder);

        $lines = $builder->build();
        $this->assertLinesNotContains(self::DATABASE, $lines);
        $this->assertLinesNotContains(self::CONNECTION, $lines);
        $this->assertLinesNotContains(self::TABLE, $lines);
        $this->assertLinesNotContains(self::FILLABLE, $lines);
        $this->assertLinesNotContains(self::GUARDED, $lines);
    }

    /**
     * Test build with table.
     *
     * @throws ReflectionException
     */
    public function testBuildWithTable(): void
    {
        $this->setConfig('addDatabaseConnection', false);
        $this->setConfig('addDatabaseTable', true);
        $this->setConfig('tables', []);

        $lines = $this->createBuilder(DatabaseInformationBuilder::class)->build();
        $this->assertLinesContains(self::DATABASE, $lines);
        $this->assertLinesNotContains(self::CONNECTION, $lines);
        $this->assertLinesContains(self::TABLE, $lines);
        $this->assertLinesNotContains(self::FILLABLE, $lines);
        $this->assertLinesNotContains(self::GUARDED, $lines);
    }

    /**
     * Test build with fillable fields.
     *
     * @throws ReflectionException
     */
    public function testBuildWithFillableFields(): void
    {
        $this->setConfig('addDatabaseConnection', false);
        $this->setConfig('addDatabaseTable', false);

        // Set guarded to none.
        $tablesConfig = $this->getConfig('tables', []);
        $tablesConfig['testbench']['lmodel']['guarded'] = [];
        $this->setConfig('tables', $tablesConfig);

        $lines = $this->createBuilder(DatabaseInformationBuilder::class)->build();
        $this->assertLinesContains(self::DATABASE, $lines);
        $this->assertLinesNotContains(self::CONNECTION, $lines);
        $this->assertLinesNotContains(self::TABLE, $lines);
        $this->assertLinesContains(self::FILLABLE, $lines);
        $this->assertLinesNotContains(self::GUARDED, $lines);
    }

    /**
     * Test build with guarded fields.
     *
     * @throws ReflectionException
     */
    public function testBuildWithGuardedFields(): void
    {
        $this->setConfig('addDatabaseConnection', false);
        $this->setConfig('addDatabaseTable', false);

        // Set guarded to none.
        $tablesConfig = $this->getConfig('tables', []);
        $tablesConfig['testbench']['lmodel']['fillable'] = [];
        $this->setConfig('tables', $tablesConfig);

        $lines = $this->createBuilder(DatabaseInformationBuilder::class)->build();
        $this->assertLinesContains(self::DATABASE, $lines);
        $this->assertLinesNotContains(self::CONNECTION, $lines);
        $this->assertLinesNotContains(self::TABLE, $lines);
        $this->assertLinesNotContains(self::FILLABLE, $lines);
        $this->assertLinesContains(self::GUARDED, $lines);
    }

    /**
     * Test build fields.
     *
     * @throws ReflectionException
     */
    public function testBuildFields(): void
    {
        $builder = $this->createBuilder(DatabaseInformationBuilder::class);
        Obj::setProperty('maxLineLength', $builder, $this->config->getMaxLineLength());

        $columnNames = [];
        for ($counter = 0; $counter < 100; $counter++) {
            $columnNames[] = 'field' . $counter;
        }

        $signature = 'testing';
        $result = Obj::callMethod(
            'buildFields',
            $builder,
            [
                'signature' => $signature,
                'columnNames' => $columnNames,
            ]
        );

        $this->assertLinesContains('protected $' . $signature, $result);
        foreach ($columnNames as $columnName) {
            $this->assertLinesContains($columnName, $result);
        }
    }
}
