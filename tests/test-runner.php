<?php declare(strict_types=1);

namespace EasyTestTest\Runner;

use EasyTest\Attribute\Test;
use EasyTest\Attribute\Fixture;

#[Fixture]
function runner(): \EasyTest\Runner {
	return new \EasyTest\Runner();
}

#[Test]
function test_runner(\EasyTest\Runner $runner): void
{
	assert($runner instanceof \EasyTest\Runner);
}
