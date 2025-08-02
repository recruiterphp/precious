<?php

namespace Precious\Type;

interface Type
{
    /**
     * Cast a value in another value of a specific type
     *
     *
     * @throws WrongTypeException
     */
    public function cast(mixed $value): mixed;
}
