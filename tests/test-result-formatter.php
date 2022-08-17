<?php declare(strict_types=1);

namespace PhpEasyestTest\ResultFormatter;

use PhpEasyest\Attribute\Fixture;
use PhpEasyest\Attribute\Test;
use PhpEasyest\ResultFormatter;

#[Test]
function testResultFormatter_Format_WhenNoFailuresAndErrors_ReturnsMatchingString(): void
{
	$passes = 1;
	$failures = [];
	$errors = [];
	$formatter = new ResultFormatter();

	$actual = $formatter->format($passes, $failures, $errors);

	$expected = <<<EOT
	All tests passed.
	
	EOT;
	assert($expected === $actual, "'''$expected''' === '''$actual'''");
}

#[Test]
function testResultFormatter_Format_WithFailure_ReturnsMatchingString(): void
{
	$passes = 1;
	$failures = [new \AssertionError('Failed test message')];
	$errors = [];
	$formatter = new ResultFormatter();

	$formattedResult = $formatter->format($passes, $failures, $errors);


	assert(strpos($formattedResult, 'Failed test message') !== false);
}

#[Test]
function testResultFormatter_Format_WithError_ReturnsMatchingString(): void
{
	$passes = 1;
	$failures = [];
	$errors = [new \Error('Error test message')];
	$formatter = new ResultFormatter();

	$formattedResult = $formatter->format($passes, $failures, $errors);


	assert(strpos($formattedResult, 'Error test message') !== false);
}

