<?php

namespace Precious\Type;

use Precious\SingletonScaffold;

class ArrayType extends PrimitiveType
{
    use SingletonScaffold;

    /**
     * @return array<mixed>
     * @throws WrongTypeException
     */
    public function cast(mixed $value): array
    {
        if (!is_array($value)) {
            self::throwWrongTypeFor($value, 'array');
        }
        return $value;
    }
}
