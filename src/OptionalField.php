<?php

namespace Precious;

use Precious\Type\Type;

class OptionalField extends RequiredField
{
    public function __construct(string $name, Type $type, private readonly mixed $defaultValue)
    {
        parent::__construct($name, $type);
    }

    /**
     * Returns the value of the field picked from an array of values
     *
     * @param array<mixed> $parameters
     * @throws WrongTypeFieldException
     *
     * @returns mixed
     */
    #[\Override]
    public function pickIn(array $parameters): mixed
    {
        try {
            return parent::pickIn($parameters);

        } catch (MissingRequiredFieldException) {
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
