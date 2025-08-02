<?php

namespace Precious;

interface Field
{
    /**
     * Returns the name of the field
     */
    public function name(): string;

    /**
     * Returns the value of the field picked from an array of values
     *
     * @param array<mixed> $parameters
     * @throws MissingRequiredFieldException
     */
    public function pickIn(array $parameters): mixed;
}
