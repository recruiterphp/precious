<?php

namespace Precious\PHPStan\Reflection;

use Exception;
use PHPStan\ShouldNotHappenException;
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
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\ParserFactory;
use Precious\Precious;

class PropertiesDetector extends NodeVisitorAbstract
{
    /** @var array<array<Property>> */
    private array $properties;

    private ?Node $inPreciousClass;

    /** @var ?Node */
    private ?Node $inPreciousClassInitMethod;

    /** @var array<string> */
    private array $names;

    private string $namespace;

    /**
     * Properties defined per classes in file
     *
     * @return array<array<Property>>
     */
    public static function inFile(string $filePath): array
    {
        $parser = new ParserFactory()->createForHostVersion();
        $fileContent = file_get_contents($filePath);
        if (!$fileContent) {
            return [];
        }
        $ast = $parser->parse($fileContent);
        if (!$ast) {
            return [];
        }
        return self::inAst($ast);
    }

    /**
     * Properties defined per classes in ast
     *
     * @param array<Node> $ast
     * @return array<array<Property>>
     */
    public static function inAst(array $ast): array
    {
        $nameResolver = new NameResolver();
        $propertiesDetector = new self();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($nameResolver);
        $nodeTraverser->addVisitor($propertiesDetector);
        $nodeTraverser->traverse($ast);
        return $propertiesDetector->properties;
    }

    public function __construct()
    {
        $this->names = [];
        $this->namespace = '';
        $this->properties = [];
        // echo PHP_EOL;
    }

    /**
     * @throws ShouldNotHappenException
     * @throws Exception
     */
    public function enterNode(Node $node): null
    {
        if ($node instanceof Namespace_) {
            $this->namespace = (string) $node->name;
        }
        if ($node instanceof Use_) {
            if (Use_::TYPE_NORMAL === $node->type) {
                /** @var UseUse $use */
                foreach ($node->uses as $use) {
                    $name = $use->name->getLast();
                    if ($use->alias) {
                        $name = $use->alias->name;
                    }
                    $this->names[$name] = (string) $use->name;
                }
            }
        }
        if ($node instanceof Class_ && Precious::class === (string) $node->extends) {
            $this->inPreciousClass = $node;
            // echo '> Precious class ' . $node->namespacedName . PHP_EOL;
            return null;
        }
        if ($node instanceof ClassMethod && $this->inPreciousClass && $node->isProtected() && 'init' === (string) $node->name) {
            $this->inPreciousClassInitMethod = $node;
            // echo '> Precious init method' . PHP_EOL;
            return null;
        }
        if ($node instanceof StaticCall && $this->inPreciousClassInitMethod) {
            if ($node->name instanceof Identifier && ('required' === (string) $node->name || 'optional' == (string) $node->name)) {
                // TODO: throw exception if arguments are not what we expect
                $isOptional = ((string) $node->name) === 'optional';
                $hasDefault = count($node->args) === 3;
                assert($node->args[0] instanceof Arg);
                $value = $node->args[0]->value;
                assert($value instanceof String_);
                $propertyName = $this->extractPropertyName($value);
                assert($node->args[1] instanceof Arg);
                $value = $node->args[1]->value;
                assert($value instanceof StaticCall);
                $propertyType = $this->extractPropertyType($value);
                if ($isOptional && !$hasDefault) {
                    $propertyType = new UnionType([new NullType(), $propertyType]);
                }
                assert($this->inPreciousClass instanceof Class_);
                $className = (string) $this->inPreciousClass->namespacedName;
                if (!array_key_exists($className, $this->properties)) {
                    $this->properties[$className] = [];
                }
                $this->properties[$className][$propertyName] = new Property($propertyName, $propertyType);
                // echo '= Precious property ' . $propertyName . ':' . get_class($propertyType) . PHP_EOL;
            }
        }

        return null;
    }

    public function leaveNode(Node $node): null
    {
        if ($node instanceof Namespace_) {
            $this->namespace = '';
        }
        if ($node instanceof Class_ && Precious::class === (string) $node->extends) {
            $this->inPreciousClass = null;
            // echo '< Precious class ' . $node->namespacedName . PHP_EOL;
            return null;
        }
        if ($node instanceof ClassMethod && $this->inPreciousClass && $node->isProtected() && 'init' === (string) $node->name) {
            $this->inPreciousClassInitMethod = null;
            // echo '< Precious init method' . PHP_EOL;
        }
        return null;
    }

    private function extractPropertyName(String_ $node) : string
    {
        return $node->value;
    }

    /**
     * @throws Exception
     */
    private function extractPropertyType(StaticCall $node) : Type
    {
        assert($node->name instanceof Identifier);
        switch ((string) $node->name) {
            case 'integerType':
                return new IntegerType();
            case 'floatType':
                return new FloatType();
            case 'booleanType':
                return new BooleanType();
            case 'stringType':
                return new StringType();
            case 'arrayType':
                return new ArrayType(new MixedType(), new MixedType());
            case 'nullType':
                return new NullType();
            case 'instanceOf':
                assert($node->args[0] instanceof Arg);
                $value = $node->args[0]->value;
                return match (get_class($value)) {
                    String_::class => new ObjectType($value->value),
                    ClassConstFetch::class => new ObjectType($this->fullyQualifiedNameOf($value)),
                    default => new MixedType(),
                };
            default:
                return new MixedType();
        }
    }

    private function fullyQualifiedNameOf(ClassConstFetch $node) : string
    {
        switch (get_class($node->class)) {
            case FullyQualified::class:
                return (string) $node->class;
            case Name::class:
                if (array_key_exists($node->class->getFirst(), $this->names)) {
                    if ($node->class->isQualified()) {
                        return (string) Name::concat($this->names[$node->class->getFirst()], (string) $node->class->slice(1, null));
                    }
                    return $this->names[$node->class->getFirst()];
                }
                return (string) Name::concat($this->namespace, $node->class);
            default:
                throw new Exception(
                    'Unable to get fully qualified name of a PhpParser\Node of kind `' . get_class($node->class) . '`'
                );
        }
    }
}
