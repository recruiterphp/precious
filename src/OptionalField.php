<?php

namespace Precious;

use Precious\Type\Type;

class OptionalField extends RequiredField
{
    private mixed $defaultValue;

    public function __construct(string $name, Type $type, mixed $defaultValue)
    {
        parent::__construct($name, $type);
        $this->defaultValue = $defaultValue;
    }

    /**
     * Returns the value of the field picked from an array of values
     *
     * @param array<mixed> $parameters
     * @throws WrongTypeFieldException
     *
     * @returns mixed
     */
    public function pickIn(array $parameters): mixed
    {
        try {
            return parent::pickIn($parameters);

        } catch (MissingRequiredFieldException $e) {
            if (null === $this->defaultValue) {
                return null;
            }
            return $this->cast($this->defaultValue);
        } catch (WrongTypeFieldException $e) {
            if (null === $this->defaultValue) {
                return null;
            }
            throw $e;
        }
    }
}
