<?php

namespace Precious\PHPStan\Reflection;

use PHPStan\Broker\Broker;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Reflection\ReflectionProvider;
use Precious\Precious;

class PreciousPropertiesClassReflectionExtension implements PropertiesClassReflectionExtension
{
    /** @var array<array<Property>> */
    private array $properties;


    /**
     * @param ClassReflection $classReflection
     * @param string $propertyName
     * @return bool
     */
    public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
    {
        if (!$classReflection->isSubclassOf(Precious::class)) {
            return false;
        }
        $filePath = $classReflection->getFileName();
        if (!$filePath) {
            return false;
        }
        if (!array_key_exists($classReflection->getName(), $this->properties)) {
            $properties = PropertiesDetector::inFile($filePath);
            foreach ($properties as $className => $classProperties) {
                if ($className === $classReflection->getName()) {
                    foreach ($classProperties as $classPropertyName => $classProperty) {
                        $classProperty->inClass($classReflection);
                        $this->properties[$className][$classPropertyName] = $classProperty;
                    }
                }
            }
        }
        if (!array_key_exists($classReflection->getName(), $this->properties)) {
            return false;
        }
        return array_key_exists($propertyName, $this->properties[$classReflection->getName()]);
    }

    /**
     * @param ClassReflection $classReflection
     * @param string $propertyName
     * @return PropertyReflection
     */
    public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
    {
        return $this->properties[$classReflection->getName()][$propertyName];
    }
}
