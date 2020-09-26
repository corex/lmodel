<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Builders;

use CoRex\Helpers\Obj;
use CoRex\Laravel\Model\Builders\DatabaseInformationBuilder;
use CoRex\Laravel\Model\Constants;
use CoRex\Laravel\Model\Helpers\Definitions\PackageDefinition;
use ReflectionException;
use Tests\CoRex\Laravel\Model\TestBase;

class DatabaseInformationBuilderTest extends TestBase
{
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
        $this->assertLinesContains($this->getTestStringConnection(), $lines);
        $this->assertLinesNotContains($this->getTestStringTable(), $lines);
        $this->assertLinesNotContainsLines($this->getTestStringsFillable(), $lines);
        $this->assertLinesNotContainsLines($this->getTestStringsGuarded(), $lines);
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
        $this->assertLinesNotContains($this->getTestStringConnection(), $lines);
        $this->assertLinesNotContains($this->getTestStringTable(), $lines);
        $this->assertLinesNotContainsLines($this->getTestStringsFillable(), $lines);
        $this->assertLinesNotContainsLines($this->getTestStringsGuarded(), $lines);
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
        $this->assertLinesNotContains($this->getTestStringConnection(), $lines);
        $this->assertLinesContains($this->getTestStringTable(), $lines);
        $this->assertLinesNotContainsLines($this->getTestStringsFillable(), $lines);
        $this->assertLinesNotContainsLines($this->getTestStringsGuarded(), $lines);
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
        $this->assertLinesNotContains($this->getTestStringConnection(), $lines);
        $this->assertLinesNotContains($this->getTestStringTable(), $lines);
        $this->assertLinesContainsLines($this->getTestStringsFillable(), $lines);
        $this->assertLinesNotContainsLines($this->getTestStringsGuarded(), $lines);
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
        $this->assertLinesNotContains($this->getTestStringConnection(), $lines);
        $this->assertLinesNotContains($this->getTestStringTable(), $lines);
        $this->assertLinesNotContainsLines($this->getTestStringsFillable(), $lines);
        $this->assertLinesContainsLines($this->getTestStringsGuarded(), $lines);
    }

    /**
     * Test build with hidden fields.
     *
     * @throws ReflectionException
     */
    public function testBuildWithHiddenFields(): void
    {
        $this->setConfig('addDatabaseConnection', false);
        $this->setConfig('addDatabaseTable', false);

        // Set guarded to none.
        $tablesConfig = $this->getConfig('tables', []);
        $tablesConfig['testbench']['lmodel']['hidden'] = [];
        $this->setConfig('tables', $tablesConfig);

        $lines = $this->createBuilder(DatabaseInformationBuilder::class)->build();
        $this->assertLinesNotContains($this->getTestStringConnection(), $lines);
        $this->assertLinesNotContains($this->getTestStringTable(), $lines);
        $this->assertLinesContainsLines($this->getTestStringsFillable(), $lines);
        $this->assertLinesContainsLines($this->getTestStringsGuarded(), $lines);
        $this->assertLinesNotContainsLines($this->getTestStringsHiddens(), $lines);
        $this->assertLinesContainsLines($this->getTestStringsCasts(), $lines);
        $this->assertLinesContainsLines($this->getTestStringsAccessors(), $lines);
    }

    /**
     * Test build with cast fields.
     *
     * @throws ReflectionException
     */
    public function testBuildWithCastFields(): void
    {
        $this->setConfig('addDatabaseConnection', false);
        $this->setConfig('addDatabaseTable', false);

        // Set guarded to none.
        $tablesConfig = $this->getConfig('tables', []);
        $tablesConfig['testbench']['lmodel']['casts'] = [];
        $this->setConfig('tables', $tablesConfig);

        $lines = $this->createBuilder(DatabaseInformationBuilder::class)->build();
        $this->assertLinesNotContains($this->getTestStringConnection(), $lines);
        $this->assertLinesNotContains($this->getTestStringTable(), $lines);
        $this->assertLinesContainsLines($this->getTestStringsFillable(), $lines);
        $this->assertLinesContainsLines($this->getTestStringsGuarded(), $lines);
        $this->assertLinesContainsLines($this->getTestStringsHiddens(), $lines);
        $this->assertLinesNotContainsLines($this->getTestStringsCasts(), $lines);
        $this->assertLinesContainsLines($this->getTestStringsAccessors(), $lines);
    }

    /**
     * Test build with accessor fields.
     *
     * @throws ReflectionException
     */
    public function testBuildWithAccessorFields(): void
    {
        $this->setConfig('addDatabaseConnection', false);
        $this->setConfig('addDatabaseTable', false);

        // Set guarded to none.
        $tablesConfig = $this->getConfig('tables', []);
        $tablesConfig['testbench']['lmodel']['appends'] = [];
        $this->setConfig('tables', $tablesConfig);

        $lines = $this->createBuilder(DatabaseInformationBuilder::class)->build();
        $this->assertLinesNotContains($this->getTestStringConnection(), $lines);
        $this->assertLinesNotContains($this->getTestStringTable(), $lines);
        $this->assertLinesContainsLines($this->getTestStringsFillable(), $lines);
        $this->assertLinesContainsLines($this->getTestStringsGuarded(), $lines);
        $this->assertLinesContainsLines($this->getTestStringsHiddens(), $lines);
        $this->assertLinesContainsLines($this->getTestStringsCasts(), $lines);
        $this->assertLinesNotContainsLines($this->getTestStringsAccessors(), $lines);
    }

    /**
     * Test build fields.
     *
     * @throws ReflectionException
     */
    public function testBuildFields(): void
    {
        $builder = $this->createBuilder(DatabaseInformationBuilder::class);

        $columnNames = [];
        for ($counter = 0; $counter < 100; $counter++) {
            $columnNames[] = 'field' . $counter;
        }

        $comment = 'Build Fields Testing';
        $signature = 'testing';
        $result = Obj::callMethod(
            'buildFields',
            $builder,
            [
                'comment' => $comment,
                'signature' => $signature,
                'columnNames' => $columnNames,
                'useIndex' => false
            ]
        );

        $this->assertSame($result, $this->buildFields($comment, $signature, $columnNames, false));
    }

    /**
     * Get test string connection.
     *
     * @return string
     */
    public function getTestStringConnection(): string
    {
        return '    protected $connection = \'testbench\';';
    }

    /**
     * Get test string table.
     *
     * @return string
     */
    public function getTestStringTable(): string
    {
        return '    protected $table = \'lmodel\';';
    }

    /**
     * Get test strings fillable.
     *
     * @return string[]
     */
    private function getTestStringsFillable(): array
    {
        $columnNames = ['code', 'number', 'string'];

        return $this->buildFields(Constants::ATTRIBUTES_FILLABLE, 'fillable', $columnNames, false);
    }

    /**
     * Get test strings guarded.
     *
     * @return string[]
     */
    private function getTestStringsGuarded(): array
    {
        $columnNames = ['code', 'number', 'string'];

        return $this->buildFields(Constants::ATTRIBUTES_GUARDED, 'guarded', $columnNames, false);
    }

    /**
     * Get test strings hidden.
     *
     * @return string[]
     */
    private function getTestStringsHiddens(): array
    {
        $columnNames = ['code', 'number', 'string'];

        return $this->buildFields(Constants::ATTRIBUTES_HIDDEN, 'hidden', $columnNames, false);
    }

    /**
     * Get test strings casts.
     *
     * @return string[]
     */
    private function getTestStringsCasts(): array
    {
        $columnNames = ['code', 'number', 'string'];

        return $this->buildFields(Constants::ATTRIBUTES_CASTS, 'casts', $columnNames, false);
    }

    /**
     * Get test strings accessors.
     *
     * @return string[]
     */
    private function getTestStringsAccessors(): array
    {
        $columnNames = ['code', 'number', 'string'];

        return $this->buildFields(Constants::ATTRIBUTES_ACCESSORS, 'appends', $columnNames, false);
    }

    /**
     * Build fields for testing (no indenting).
     *
     * @param string $comment
     * @param string $signature
     * @param string[] $columnNames
     * @param bool $useIndex
     * @return string[]
     */
    private function buildFields(string $comment, string $signature, array $columnNames, bool $useIndex): array
    {
        // Create first line to build on.
        $lines = [
            $this->indent(1) . '// ' . $comment,
            $this->indent(1) . 'protected $' . $signature . ' = ['
        ];

        $columnCounter = 1;
        foreach ($columnNames as $index => $columnName) {
            $line = $this->indent(2);
            if ($useIndex) {
                $line .= '\'' . $index . '\' => ';
            }
            $lines[] = $line . '\'' . $columnName . '\'';

            // Increase count and add ','.
            $columnCounter++;
            if ($columnCounter <= count($columnNames)) {
                $lines[count($lines) - 1] .= ',';
            }
        }

        // Footer.
        $lines[] = $this->indent(1) . '];';

        return $lines;
    }
}
