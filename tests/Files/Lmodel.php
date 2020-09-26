<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Files;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Tester\TesterTrait;
use Symfony\Contracts\Translation\TranslatorTrait;

/**
 * @property int $id
 * @property string $code Code for constants etc.
 * @property int $number Number to have fun with.
 * @property string $string To use in constants etc.
 */
class Lmodel extends Model
{
    use TesterTrait;
    use TranslatorTrait;

    // Constants for numbers.
    public const NUMCODE_1S = 1;
    public const NUMCODE_10S = 10;
    public const NUMCODE_2S = 2;
    public const NUMCODE_3S = 3;
    public const NUMCODE_4S = 4;
    public const NUMCODE_5S = 5;
    public const NUMCODE_6S = 6;
    public const NUMCODE_7S = 7;
    public const NUMCODE_8S = 8;
    public const NUMCODE_9S = 9;

    // Constants for strings.
    public const STRCODE_1S = 'String 1';
    public const STRCODE_10S = 'String 10';
    public const STRCODE_2S = 'String 2';
    public const STRCODE_3S = 'String 3';
    public const STRCODE_4S = 'String 4';
    public const STRCODE_5S = 'String 5';
    public const STRCODE_6S = 'String 6';
    public const STRCODE_7S = 'String 7';
    public const STRCODE_8S = 'String 8';
    public const STRCODE_9S = 'String 9';

    // Timestamps.
    public $timestamps = false;

    // Database.
    protected $connection = 'main';
    protected $table = 'lmodel';
    protected $fillable = ['code', 'number', 'string'];
    protected $guarded = ['code', 'number', 'string'];
    protected $hidden = ['code', 'number', 'string'];
    protected $casts = ['field1' => 'code', 'field2' => 'number', 'field3' => 'string'];
    protected $appends = ['code', 'number', 'string'];

    /* ---- Everything after this line will be preserved. ---- */

    /**
     * Test.
     */
    public function test(): void
    {
    }
}
