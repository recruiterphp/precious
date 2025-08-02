<?php

namespace Precious\Type;

use Precious\SingletonScaffold;

class FloatType extends PrimitiveType
{
    use SingletonScaffold;

    /**
     * @throws WrongTypeException
     */
    public function cast(mixed $value): float
    {
        if (!is_numeric($value)) {
            self::throwWrongTypeFor($value, 'float');
        }
        return (float) $value;
    }
}
