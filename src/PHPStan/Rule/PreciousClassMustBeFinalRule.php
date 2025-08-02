<?php

namespace Precious\PHPStan\Rule;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Precious\Precious;

/**
 * @implements Rule<Class_>
 */
readonly class PreciousClassMustBeFinalRule implements Rule
{
	public function __construct(private ReflectionProvider $broker)
	{
	}

    /**
	 * @return string Node we are interested in
	 */
    public function getNodeType(): string
	{
		return Class_::class;
	}

    /**
	 * @param Node $node
	 * @param Scope $scope
	 * @throws ShouldNotHappenException
	 * @return array<RuleError> errors
	 */
    public function processNode(Node $node, Scope $scope): array
	{
        assert($node instanceof Class_);
        $currentClassName = $node->namespacedName->toString();
        $currentClassReflection = $this->broker->getClass($currentClassName);
        $parentClassReflection = $currentClassReflection->getParentClass();
        if (!$parentClassReflection) {
            return [];
        }
        if ($parentClassReflection->getName() !== Precious::class) {
            return [];
        }
        if ($currentClassReflection->isFinal()) {
            return [];
        }

        return [RuleErrorBuilder::message('A subclass of Precious\Precious must be declared final')->build()];
    }
}
