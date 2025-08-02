<?php

namespace Precious\PHPStan\Reflection;

use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Type;

class Property implements PropertyReflection
{
    private ClassReflection $class;

    public function __construct(private readonly string $name, private readonly Type $type) {
    }

    public function inClass(ClassReflection $class) : void
    {
        $this->class = $class;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType() : Type
    {
        return $this->type;
    }

    public function getReadableType(): Type
    {
        return $this->type;
    }

    public function getWritableType(): Type
    {
        return $this->type;
    }

	public function getDeclaringClass() : ClassReflection
    {
        return $this->class;
    }

	public function isStatic() : bool
    {
        return false;
    }

	public function isPrivate() : bool
    {
        return false;
    }

	public function isPublic() : bool
    {
        return true;
    }

    public function isReadable() : bool
    {
        return true;
    }

    public function isWritable() : bool
    {
        return false;
    }

    public function isInternal() : TrinaryLogic
    {
        return TrinaryLogic::createMaybe();
    }

    public function isDeprecated() : TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }

    public function canChangeTypeAfterAssignment() : bool
    {
        return false;
    }

    public function getDeprecatedDescription() : ?string
    {
        return null;
    }

    public function getDocComment() : ?string
    {
        // TODO: implement
        return null;
    }
}
