<?php

namespace Precious\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<Class_>
 */
class NullRule implements Rule
{
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
	 * @return array<IdentifierRuleError> errors
     */
    public function processNode(Node $node, Scope $scope): array
	{
        return [];
    }
}
