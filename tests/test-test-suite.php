<?php 

namespace EasyTestTest\TestSuite;

use EasyTest\Attribute\Fixture;
use EasyTest\Attribute\Test;
use EasyTest\TestSuite;
use Generator;

#[Test]
function testTestSuite_GetTests_WhenPrepareCalled_ReturnsMatchingArray(TestSuite $testSuite): void
{
	$testSuite->prepare();

	$actual = $testSuite->getTests();

	$expected = [
		[
			'function' => 'EasyTestTest\\TestSuite\\exampleTest',
			'arguments' => [
				'EasyTestTest\\TestSuite\\exampleFixture' => 'int',
			],
		],
	];
	assert($expected === $actual, 'Expected: ' . json_encode($expected) . ' Actual: ' . json_encode($actual));
}

#[Test]
function testTestSuite_GetFixture_WhenNameAndTypeProvided_ReturnsMatchingFunctionName(TestSuite $testSuite): void
{
	$testSuite->prepare();

	$actual = $testSuite->hasFixture('EasyTestTest\\TestSuite\\exampleFixture', 'int');

	assert($actual);
}

#[Fixture]
function testSuite(): Generator
{
	yield new TestSuite([
		'\\EasyTestTest\\TestSuite\\exampleTest', 
		'\\EasyTestTest\\TestSuite\\exampleFixture',
	]);
}

#[Test]
function exampleTest(int $exampleFixture): void {

}

#[Fixture] 
function exampleFixture(): int {
	return 1;
}
