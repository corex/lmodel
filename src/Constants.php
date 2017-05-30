<?php

namespace CoRex\Laravel\Model;

class Constants
{
    const DEFAULT_INDENT = '    ';
    const PRESERVED_IDENTIFIER = '/' . '* ---- Everything after this line will be preserved. ---- *' . '/';
    const STANDARD_REPLACE = ['Æ' => 'AE', 'Ø' => 'OE', 'Å' => 'AA'];
    const DEFAULT_MODEL_CLASS = Model::class;
    const DEFAULT_LINE_LENGTH = 120;
}