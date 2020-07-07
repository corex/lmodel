<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model;

class Constants
{
    public const DEFAULT_INDENT = '    ';
    public const MAX_LINE_LENGTH = 120;
    public const PRESERVED_IDENTIFIER = '/* ---- Everything after this line will be preserved. ---- */';
    public const STANDARD_REPLACES = [
        'Æ' => 'AE',
        'Ø' => 'OE',
        'Å' => 'AA'
    ];
    public const STANDARD_COLUMN_MAPPINGS = [
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
        'timestamp' => 'int',
    ];
    public const ELOQUENT_CREATED_AT = 'created_at';
    public const ELOQUENT_UPDATED_AT = 'updated_at';
}
