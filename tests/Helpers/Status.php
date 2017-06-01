<?php

namespace App\Models\Test;

use CoRex\Laravel\Model\Model;

/**
 * @property integer $id [TYPE=INTEGER, NULLABLE=0, DEFAULT=""]
 * @property string $name [TYPE=STRING, NULLABLE=0, DEFAULT=""]
 * @property string $value [TYPE=STRING, NULLABLE=0, DEFAULT=""]
 */
class Status extends Model
{
    // Constants.
    const CONSTANT1 = 1;
    const CONSTANT2 = 2;
    const CONSTANT3 = 3;
    const CONSTANT4 = 4;

    // Attributes.
    protected $connection = 'test_connection';
    protected $table = 'status';
    protected $fillable = ['id', 'name', 'value'];
    protected $guarded = [];

    /* ---- Everything after this line will be preserved. ---- */

    /**
     * Preserve this function.
     *
     * @return string
     */
    public function preserveThisFunction()
    {
        return 'preserved';
    }
}
