<?php declare(strict_types=1);

namespace EasyTestTest\Test;

use EasyTest\Attribute\Test;
use EasyTest\Test as TestCase;
use EasyTest\TestSuiteInterface;


#[Test]
function testTest_Run_WhenNoArguments_RunsTestFunction(): void
{
	GlobalScope::$var = 0;
	$testSuite = new TestSuiteMock();
	$test = new TestCase($testSuite, 'EasyTestTest\\Test\\test_function', []);

	$test->run();

	assert(GlobalScope::$var === 1);
}

#[Test]
function testTest_Run_WhenArgumentWithGenerator_RunsTestFunctionForEachGeneratorValue(): void
{
	GlobalScope::$var = 0;
	$testSuite = new TestSuiteMock();
	$test = new TestCase(
		$testSuite, 
		'EasyTestTest\\Test\\test_function_with_generator_argument', 
		['EasyTestTest\\Test\\generator_fixture' => 'int']
	);

	$test->run();

	assert(GlobalScope::$var === 6);
}

class TestSuiteMock implements TestSuiteInterface {

    public function prepare(): void { }

	public function getTests(): array {
		return [];
	}

	public function hasFixture(string $fixtureName, string $fixtureType): bool {
		return true;
	}

	public function getSetups(): array {
		return [];
	}

	public function getTearDowns(): array {
		return [];
	}
}

class GlobalScope {
	public static int $var = 0;
} 

function test_function() {
	GlobalScope::$var = 1;
}

function test_function_with_generator_argument(int $generator_fixture) {
	GlobalScope::$var += $generator_fixture;
}

function generator_fixture(): \Generator {
	yield 1;
	yield 2;
	yield 3;
}
