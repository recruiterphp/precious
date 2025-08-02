<?php

namespace Precious;

use Precious\Type\PrimitiveType;
use Precious\Type\ClassType;
use Precious\Type\Type;
use JsonSerializable;

abstract class Precious implements JsonSerializable
{
    /**
     * @var array<mixed>
     */
    private readonly array $parameters;

    /**
     * @var array<Fields>
     */
    private static array $fields = [];

    /**
     * Returns a new instance of a value object
     *
     * @param array<mixed> $candidateParameters
     * @throws NameClashFieldException
     * @throws MissingRequiredFieldException
     * @throws MissingRequiredFieldException
     * @throws WrongTypeFieldException
     * @returns self
     */
    public final function __construct(array $candidateParameters = [])
    {
        if (!array_key_exists(static::class, self::$fields)) {
            self::$fields[static::class] = new Fields($this->init());
        }
        $parameters = [];
        /** @var Field $field */
        foreach (self::$fields[static::class] as $field) {
            $parameters[$field->name()] = $field->pickIn($candidateParameters);
        }

        $this->parameters = $parameters;
    }

    /**
     * @throws MissingRequiredFieldException
     * @throws WrongTypeFieldException
     * @throws NameClashFieldException
     */
    public function set(string $name, mixed $value): static
    {
        return new static(array_merge($this->parameters, [$name => $value]));
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * Unsafe version, use only as last resource
     *
     * @returns mixed The field value or null if missing
     */
    public function get(string $name): mixed
    {
        if (!array_key_exists($name, $this->parameters)) {
            return null;
        }
        return $this->parameters[$name];
    }

    public function __get(string $name): mixed
    {
        if (!array_key_exists($name, $this->parameters)) {
            throw new UnknownFieldException("Unknown field `$name`");
        }
        return $this->parameters[$name];
    }

    public function __set(string $name, mixed $value): void
    {
        if (!array_key_exists($name, $this->parameters)) {
            throw new UnknownFieldException("Unknown field `$name`");
        }
        throw new ReadOnlyFieldException("Cannot write field `$name`");
    }

    public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->parameters;
    }

    /**
     * Returns a new instance where the given fields replace existing ones.
     * @param array<mixed> $parameters
     * @throws MissingRequiredFieldException
     * @throws WrongTypeFieldException
     * @throws NameClashFieldException
     */
    public function with(array $parameters = []): static
    {
        return new static(array_merge(
            $this->parameters,
            $parameters
        ));
    }

    protected static function required(string $fieldName, Type $fieldType) : Field
    {
        return new RequiredField($fieldName, $fieldType);
    }

    protected static function optional(string $fieldName, Type $fieldType, mixed $defaultValue = null): Field
    {
        return new OptionalField($fieldName, $fieldType, $defaultValue);
    }

    protected static function integerType() : Type
    {
        return PrimitiveType::integerType();
    }

    protected static function floatType() : Type
    {
        return PrimitiveType::floatType();
    }

    protected static function booleanType() : Type
    {
        return PrimitiveType::booleanType();
    }

    protected static function arrayType() : Type
    {
        return PrimitiveType::arrayType();
    }

    protected static function stringType() : Type
    {
        return PrimitiveType::stringType();
    }

    protected static function nullType() : Type
    {
        return PrimitiveType::nullType();
    }

    protected static function mixedType() : Type
    {
        return PrimitiveType::mixedType();
    }

    protected static function instanceOf(string $class) : Type
    {
        return ClassType::instanceOf($class);
    }

    /**
     * @return Field[]
     */
    abstract protected function init() : array;
}
