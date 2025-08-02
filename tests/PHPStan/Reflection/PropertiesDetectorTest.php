<?php

namespace Precious\PHPStan\Reflection;

use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use PHPUnit\Framework\TestCase;
use Precious\Example\C;
use Precious\Example\FullyQualifiedClassNameBarrage;
use Precious\Example\OneOptionalProperty;
use Precious\Example\OneOptionalNullableProperty;
use Precious\Example\OneRequiredProperty;
use Precious\Example\OneRequiredPropertyPerType;
use Precious\Precious;

class PropertiesDetectorTest extends TestCase
{
    public function testOneRequiredProperty(): void
    {
        $properties = PropertiesDetector::inFile(__DIR__ . '/data/OneRequiredProperty.php');
        $this->assertCount(1, $properties);
        $this->assertArrayHasKey(OneRequiredProperty::class, $properties);

        $properties = $properties[OneRequiredProperty::class];
        $this->assertCount(1, $properties);
        $this->assertEquals($properties['a'], new Property('a', new IntegerType()));
    }

    public function testOneOptionalProperty(): void
    {
        $properties = PropertiesDetector::inFile(__DIR__ . '/data/OneOptionalProperty.php');
        $this->assertCount(1, $properties);
        $this->assertArrayHasKey(OneOptionalProperty::class, $properties);

        $properties = $properties[OneOptionalProperty::class];
        $this->assertCount(1, $properties);
        $this->assertEquals($properties['a'], new Property('a', new IntegerType()));
    }

    public function testOneOptionalNullableProperty(): void
    {
        $properties = PropertiesDetector::inFile(__DIR__ . '/data/OneOptionalNullableProperty.php');
        $this->assertCount(1, $properties);
        $this->assertArrayHasKey(OneOptionalNullableProperty::class, $properties);

        $properties = $properties[OneOptionalNullableProperty::class];
        $this->assertCount(1, $properties);
        $this->assertEquals($properties['a'], new Property('a', new UnionType([new NullType(), new IntegerType()])));
    }

    public function testOneRequiredPropertyPerType(): void
    {
        $properties = PropertiesDetector::inFile(__DIR__ . '/data/OneRequiredPropertyPerType.php');
        $this->assertCount(1, $properties);
        $this->assertArrayHasKey(OneRequiredPropertyPerType::class, $properties);

        $properties = $properties[OneRequiredPropertyPerType::class];
        $this->assertCount(8, $properties);
        $this->assertEquals($properties['a'], new Property('a', new IntegerType()));
        $this->assertEquals($properties['b'], new Property('b', new FloatType()));
        $this->assertEquals($properties['c'], new Property('c', new StringType()));
        $this->assertEquals($properties['d'], new Property('d', new BooleanType()));
        $this->assertEquals($properties['e'], new Property('e', new ArrayType(new MixedType(), new MixedType())));
        $this->assertEquals($properties['f'], new Property('f', new NullType()));
        $this->assertEquals($properties['g'], new Property('g', new MixedType()));
        $this->assertEquals($properties['h'], new Property('h', new ObjectType(Precious::class)));
    }

    public function testFullyQualifiedClassNameBarrage(): void
    {
        $properties = PropertiesDetector::inFile(__DIR__ . '/data/FullyQualifiedClassNameBarrage.php');
        $this->assertCount(1, $properties);
        $this->assertArrayHasKey(FullyQualifiedClassNameBarrage::class, $properties);

        $properties = $properties[FullyQualifiedClassNameBarrage::class];
        $this->assertCount(7, $properties);
        $this->assertEquals($properties['a'], new Property('a', new ObjectType(Precious::class)));
        $this->assertEquals($properties['b'], new Property('b', new ObjectType(Type::class)));
        $this->assertEquals($properties['c'], new Property('c', new ObjectType(IntegerType::class)));
        $this->assertEquals($properties['d'], new Property('d', new ObjectType(FloatType::class)));
        $this->assertEquals($properties['e'], new Property('e', new ObjectType(ObjectType::class)));
        $this->assertEquals($properties['f'], new Property('f', new ObjectType(Precious::class)));
        $this->assertEquals($properties['h'], new Property('h', new ObjectType(C::class)));
    }
}
