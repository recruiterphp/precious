<?php

namespace Precious\Type;

use Exception;

readonly class ClassType implements Type
{
    /**
     * @throws Exception
     */
    public static function instanceOf(string $class) : self
    {
        if (!class_exists($class) && !interface_exists($class)) {
            throw new Exception("Unknown class {$class}");
        }
        return new self($class);
    }

    private function __construct(private string $class)
    {
    }

    /**
     * @throws WrongTypeException
     */
    public function cast(mixed $value): ?object
    {
        if (is_null($value)) {
            return null;
        }
        if (!is_object($value)) {
            $type = gettype($value);
            throw new WrongTypeException(
                "Value is not an instance of `{$this->class}` but a `{$type}`"
            );
        }
        if (!($value instanceof $this->class)) {
            $currentClass = get_class($value);
            throw new WrongTypeException(
                "Value is not an instance of `{$this->class}` but an instance of `{$currentClass}`"
            );
        }
        return $value;
    }
}
