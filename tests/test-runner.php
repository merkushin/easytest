<?php declare(strict_types=1);

use PhpEasyest\Attribute\Test;
use PhpEasyest\Attribute\Fixture;

#[Fixture]
function runner(): \PhpEasyest\Runner {
	return new \PhpEasyest\Runner();
}

#[Test]
function test_runner(\PhpEasyest\Runner $runner): void
{
	assert($runner instanceof \PhpEasyest\Runner);
}
