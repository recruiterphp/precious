<?php

namespace Precious;

use Precious\Type\Type;
use Precious\Type\WrongTypeException;

class RequiredField implements Field
{
    public function __construct(private readonly string $name, private readonly Type $type)
    {
    }

    /**
     * Returns the name of the field
     *
     * @returns string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Returns the value of the field picked from an array of values
     *
     * @param array<mixed> $parameters
     * @throws WrongTypeFieldException
     * @throws MissingRequiredFieldException
     */
    public function pickIn(array $parameters): mixed
    {
        if (!array_key_exists($this->name, $parameters)) {
            throw new MissingRequiredFieldException(
                "Missing required field `{$this->name}`"
            );
        }
        return $this->cast($parameters[$this->name]);
    }

    /**
     * @throws WrongTypeFieldException
     */
    protected function cast(mixed $value): mixed
    {
        try {
            return $this->type->cast($value);

        } catch (WrongTypeException $e) {
            throw new WrongTypeFieldException(
                "Wrong type for field `{$this->name}`. {$e->getMessage()}"
            );
        }
    }
}
