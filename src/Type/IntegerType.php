<?php

namespace Precious\Type;

use Precious\SingletonScaffold;

class IntegerType extends PrimitiveType
{
    use SingletonScaffold;

    /**
     * @throws WrongTypeException
     */
    public function cast(mixed $value): int
    {
        if (!is_numeric($value)) {
            self::throwWrongTypeFor($value, 'integer');
        }
        return (int) $value;
    }
}
