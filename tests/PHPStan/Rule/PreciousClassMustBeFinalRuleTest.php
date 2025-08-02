<?php

namespace Precious\PHPStan\Rule;

use Precious\PHPStan\Rule\PreciousClassMustBeFinalRule;
use PHPStan\Testing\RuleTestCase;
use PHPStan\Rules\Rule;

/**
 * @extends RuleTestCase<PreciousClassMustBeFinalRule>
 */
class PreciousClassMustBeFinalRuleTest extends RuleTestCase
{
	protected function getRule(): Rule
	{
        $broker = $this->createReflectionProvider();
		return new PreciousClassMustBeFinalRule($broker);
	}

    public function testRule(): void
	{
		$this->analyse([__DIR__ . '/data/NotFinalClass.php'], [
			[
                'A subclass of Precious\Precious must be declared final',
				7,
			],
		]);
	}
}
