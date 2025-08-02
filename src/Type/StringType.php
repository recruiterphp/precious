<?php

namespace Precious\Type;

use Precious\SingletonScaffold;

class StringType extends PrimitiveType
{
    use SingletonScaffold;

    /**
     * @throws WrongTypeException
     */
    public function cast(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (string) $value;
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if ($value instanceof \Stringable) {
            return $value->__toString();
        }
        self::throwWrongTypeFor($value, 'string');

        return ''; // Unreachable but PHPStan cannot detect this
    }
}
