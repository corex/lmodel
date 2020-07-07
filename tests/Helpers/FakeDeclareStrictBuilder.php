<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Helpers;

use CoRex\Laravel\Model\Builders\DeclareStrictBuilder;

class FakeDeclareStrictBuilder extends DeclareStrictBuilder
{
    /**
     * Build.
     *
     * @return string[]
     */
    public function build(): array
    {
        $result = parent::build();

        // Insert word "test" at start of first line to check on later.
        $result[0] = 'test' . $result[0];

        return $result;
    }
}
