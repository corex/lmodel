<?php

namespace CoRex\Laravel\Model;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    // Disable Eloquent timestamps.
    public $timestamps = false;
}