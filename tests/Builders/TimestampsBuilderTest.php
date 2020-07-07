<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Builders;

use CoRex\Laravel\Model\Builders\TimestampsBuilder;
use Tests\CoRex\Laravel\Model\TestBase;

class TimestampsBuilderTest extends TestBase
{
    private const TIMESTAMPS = '// Timestamps.';

    private const CREATED_AT = 'const CREATED_AT = \'created_at_test\';';

    private const UPDATED_AT = 'const UPDATED_AT = \'updated_at_test\';';

    private const DATE_FORMAT = 'protected $dateFormat = \'U\';';

    private const TIMESTAMPS_FALSE = 'public $timestamps = false;';

    /**
     * Test build handled.
     */
    public function testBuildHandled(): void
    {
        // Change settings for timestamps.
        $configTables = $this->getConfig('tables', []);
        $this->setConfig('tables', $configTables);

        $lines = $this->createBuilder(TimestampsBuilder::class)->build();
        $this->assertLinesContains(self::TIMESTAMPS, $lines);
        $this->assertLinesContains(self::CREATED_AT, $lines);
        $this->assertLinesContains(self::UPDATED_AT, $lines);
        $this->assertLinesContains(self::DATE_FORMAT, $lines);
        $this->assertLinesNotContains(self::TIMESTAMPS_FALSE, $lines);
    }

    /**
     * Test build not handled created_at.
     */
    public function testBuildNotHandledCreatedAt(): void
    {
        // Change settings for timestamps.
        $configTables = $this->getConfig('tables', []);
        $configTables['testbench']['lmodel']['created_at'] = null;
        $this->setConfig('tables', $configTables);

        $lines = $this->createBuilder(TimestampsBuilder::class)->build();
        $this->assertLinesContains(self::TIMESTAMPS, $lines);
        $this->assertLinesNotContains(self::CREATED_AT, $lines);
        $this->assertLinesNotContains(self::UPDATED_AT, $lines);
        $this->assertLinesNotContains(self::DATE_FORMAT, $lines);
        $this->assertLinesContains(self::TIMESTAMPS_FALSE, $lines);
    }

    /**
     * Test build not handled updated_at.
     */
    public function testBuildNotHandledUpdatedAt(): void
    {
        // Change settings for timestamps.
        $configTables = $this->getConfig('tables', []);
        $configTables['testbench']['lmodel']['updated_at'] = null;
        $this->setConfig('tables', $configTables);

        $lines = $this->createBuilder(TimestampsBuilder::class)->build();
        $this->assertLinesContains(self::TIMESTAMPS, $lines);
        $this->assertLinesNotContains(self::CREATED_AT, $lines);
        $this->assertLinesNotContains(self::UPDATED_AT, $lines);
        $this->assertLinesNotContains(self::DATE_FORMAT, $lines);
        $this->assertLinesContains(self::TIMESTAMPS_FALSE, $lines);
    }

    /**
     * Test build not handled updated_at.
     */
    public function testBuildNotHandledDateFormat(): void
    {
        // Change settings for timestamps.
        $configTables = $this->getConfig('tables', []);
        $configTables['testbench']['lmodel']['updated_at'] = null;
        $this->setConfig('tables', $configTables);

        $lines = $this->createBuilder(TimestampsBuilder::class)->build();
        $this->assertLinesContains(self::TIMESTAMPS, $lines);
        $this->assertLinesNotContains(self::CREATED_AT, $lines);
        $this->assertLinesNotContains(self::UPDATED_AT, $lines);
        $this->assertLinesNotContains(self::DATE_FORMAT, $lines);
        $this->assertLinesContains(self::TIMESTAMPS_FALSE, $lines);
    }

    /**
     * Test build not handled updated_at.
     */
    public function testBuildNotHandled(): void
    {
        // Change settings for timestamps.
        $configTables = $this->getConfig('tables', []);
        $configTables['testbench']['lmodel']['date_format'] = null;
        $this->setConfig('tables', $configTables);

        $lines = $this->createBuilder(TimestampsBuilder::class)->build();
        $this->assertLinesContains(self::TIMESTAMPS, $lines);
        $this->assertLinesContains(self::CREATED_AT, $lines);
        $this->assertLinesContains(self::UPDATED_AT, $lines);
        $this->assertLinesNotContains(self::DATE_FORMAT, $lines);
        $this->assertLinesNotContains(self::TIMESTAMPS_FALSE, $lines);
    }
}
