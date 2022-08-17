<?php 

namespace PhpEasyestTest\TestSuite;

use PhpEasyest\Attribute\Fixture;
use PhpEasyest\Attribute\Ignore;
use PhpEasyest\Attribute\Test;
use PhpEasyest\TestSuite;

#[Test]
function testTestSuite_GetTests_WhenPrepareCalled_ReturnsMatchingArray(TestSuite $testSuite): void
{
	$testSuite->prepare();

	$actual = $testSuite->getTests();

	$expected = [
		[
			'function' => 'PhpEasyestTest\\TestSuite\\exampleTest',
			'arguments' => [
				'PhpEasyestTest\\TestSuite\\exampleFixture' => 'int',
			],
		],
	];
	assert($expected === $actual, 'Expected: ' . json_encode($expected) . ' Actual: ' . json_encode($actual));
}

#[Test]
function testTestSuite_GetFixture_WhenNameAndTypeProvided_ReturnsMatchingFunctionName(TestSuite $testSuite): void
{
	$testSuite->prepare();

	$actual = $testSuite->hasFixture('PhpEasyestTest\\TestSuite\\exampleFixture', 'int');

	assert($actual);
}

#[Fixture]
function testSuite(): TestSuite
{
	return new TestSuite([
		'\\PhpEasyestTest\\TestSuite\\exampleTest', 
		'\\PhpEasyestTest\\TestSuite\\exampleFixture',
	]);
}

#[Test]
function exampleTest(int $exampleFixture): void {

}

#[Fixture] 
function exampleFixture(): int {
	return 1;
}
