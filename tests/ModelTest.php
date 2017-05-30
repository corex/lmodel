<?php

use CoRex\Laravel\Model\Model;
use Orchestra\Testbench\TestCase;

class ModelTest extends TestCase
{
    /**
     * Test timestamp.
     */
    public function testTimestamp()
    {
        $model = new Model();
        $this->assertFalse($model->timestamps);
    }
}
