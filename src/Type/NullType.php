<?php

namespace Precious\Type;

use Precious\SingletonScaffold;

class NullType extends PrimitiveType
{
    use SingletonScaffold;

    /**
     * @throws WrongTypeException
     */
    public function cast(mixed $value): null
    {
        if (!is_null($value)) {
            self::throwWrongTypeFor($value, 'null');
        }
        return null;
    }
}
