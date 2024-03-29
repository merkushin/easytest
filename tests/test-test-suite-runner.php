<?php declare(strict_types=1);

namespace EasyTestTest\TestSuiteRunner;

use EasyTest\Attribute\Fixture;
use EasyTest\Attribute\Test;
use EasyTest\TestSuite;
use EasyTest\TestSuiteRunner;

#[Fixture]
function runner(): TestSuiteRunner
{
    $testSuite = new TestSuite(['EasyTestTest\\TestSuiteRunner\\test_one', 'EasyTestTest\\TestSuiteRunner\\test_one']);
    $testSuite->prepare();

    return new TestSuiteRunner($testSuite);
}

#[Test]
function testTestSuiteRunner_Run_WhenCalled_YieldsResuts(TestSuiteRunner $runner): void {
    $results = $runner->run();
    
    $actual = iterator_to_array($results);
    $expected = ['.', '.'];
    assert($actual === $expected);
}

#[Test]
function testTestSuiteRunner_GetPasses_AfterRun_ReturnsPasses(TestSuiteRunner $runner): void {
    $result = $runner->run();
    iterator_to_array($result);

    $passes = $runner->getPasses();

    assert($passes === 2, "$passes === 2");
}

#[Test]
function testTestSuiteRunner_GetFailures_AfterRun_ReturnsEmptyArray(TestSuiteRunner $runner): void {
    $result = $runner->run();
    iterator_to_array($result);

    $failures = $runner->getFailures();

    assert([] === $failures);
}

#[Test]
function testTestSuiteRunner_GetErrors_AfterRun_ReturnsEmptyArray(TestSuiteRunner $runner): void {
    $result = $runner->run();
    iterator_to_array($result);

    $errors = $runner->getErrors();

    assert([] === $errors);
}

#[Test]
function test_one(): void {
    assert(true);
}

#[Test]
function test_two(): void {
    assert(true);
}

