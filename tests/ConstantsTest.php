<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model;

use CoRex\Helpers\Obj;
use CoRex\Laravel\Model\Constants;
use PHPUnit\Framework\TestCase;

class ConstantsTest extends TestCase
{
    // Constants to test.
    private const CONSTANTS = [
        'DEFAULT_INDENT' => '    ',
        'MAX_LINE_LENGTH' => 120,
        'PRESERVED_IDENTIFIER' => '/* ---- Everything after this line will be preserved. ---- */',
        'STANDARD_REPLACES' => [
            'Æ' => 'AE',
            'Ø' => 'OE',
            'Å' => 'AA'
        ],
        'STANDARD_COLUMN_MAPPINGS' => [
            'varchar' => 'string',
            'longblob' => 'string',
            'longtext' => 'string',
            'datetime' => 'string',
            'date' => 'string',
            'text' => 'string',
            'integer' => 'int',
            'tinyint' => 'int',
            'bigint' => 'int',
            'smallint' => 'int',
            'timestamp' => 'int'
        ],
        'ELOQUENT_CREATED_AT' => 'created_at',
        'ELOQUENT_UPDATED_AT' => 'updated_at',
        'CHARACTERS' => ['-', '.', ',', ';', ':', ' ', '?', '\'', '"', '#', '%', '&', '/', '\\', '(', ')'],
        'PHPDOC_HEADER' => [
            'This file has been auto-generated.',
            'Update settings in config file for table',
            'and run "make:models %s %s".',
            ''
        ],
        'ATTRIBUTES_FILLABLE' => 'The attributes that are mass assignable.',
        'ATTRIBUTES_GUARDED' => 'The attributes that aren\'t mass assignable.',
        'ATTRIBUTES_HIDDEN' => 'The attributes that should be hidden for arrays.',
        'ATTRIBUTES_CASTS' => 'The attributes that should be cast to native types.',
        'ATTRIBUTES_ACCESSORS' => 'The accessors to append to the model\'s array form.'
    ];

    /**
     * Test constants.
     */
    public function testConstants(): void
    {
        $constants = Obj::getPublicConstants(Constants::class);

        // Test that all constants exists.
        foreach (self::CONSTANTS as $testConstant => $testValue) {
            $this->assertArrayHasKey($testConstant, $constants);
            $this->assertSame($testValue, $constants[$testConstant]);
        }

        // Test that all constants are tested.
        foreach ($constants as $constant => $value) {
            $this->assertArrayHasKey($constant, self::CONSTANTS);
            $this->assertSame($value, self::CONSTANTS[$constant]);
        }
    }
}
