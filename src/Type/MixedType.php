<?php

namespace Precious\Type;

use Precious\SingletonScaffold;

class MixedType extends PrimitiveType
{
    use SingletonScaffold;

    public function cast(mixed $value): mixed
    {
        return $value;
    }
}
