<?php declare(strict_types=1);

namespace PhpEasyestTest\ArgumentParser;

use PhpEasyest\Attribute\Fixture;
use PhpEasyest\Attribute\Ignore;
use PhpEasyest\Attribute\Setup;
use PhpEasyest\Attribute\TearDown;
use PhpEasyest\Attribute\Test;
use PhpEasyest\ArgumentParser;

#[Fixture]
function parser(): ArgumentParser
{
	return new ArgumentParser();
}

#[Test]
function testParser_GetTestsPath_WithEmptyArguments_ReturnsDefaultValue(ArgumentParser $parser): void
{
	$parser->parse([]);
	$testsPath = $parser->getTestsPath('./tests');
	assert('./tests' === $testsPath);
}

#[Test]
function testParser_GetTestsPath_WithTestsPathArgument_ReturnsTestsPath(ArgumentParser $parser): void
{
	$parser->parse(['a', 'b', 'c']);
	$testsPath = $parser->getTestsPath();
	assert('b' === $testsPath);
}
