<?php

namespace Precious\Type;

use Precious\SingletonScaffold;

class BooleanType extends PrimitiveType
{
    use SingletonScaffold;

    public function cast(mixed $value): bool
    {
        return (bool) $value;
    }
}
