<?php declare(strict_types=1);

namespace EasyTestTest\TestArguments;

use EasyTest\Attribute\Fixture;
use EasyTest\Attribute\Test;
use EasyTest\TestArguments;
use EasyTest\TestSuite;
use Generator;

#[Test]
function testTestArguments_Prepare_WhenFixtureProvided_ReturnsArrayWithFixtureValue(): void
{
    $testSuite = new TestSuite([
        '\\EasyTestTest\\TestArguments\\exampleFixture',
    ]);
    $testSuite->prepare();

    $actual = TestArguments::prepare($testSuite, ['EasyTestTest\\TestArguments\\exampleFixture' => 'int']);

    $expected = [ 1 ];
    assert($expected === $actual->getPrepared(), 'Expected: ' . var_export($expected, true) . '. Actual: ' . var_export($actual->getPrepared(), true));
}

#[Test]
function testTestArguments_HasGenerators_WhenNoGeneratorProvided_ReturnsFalse(): void
{
    $testArguments = new TestArguments([1]);

    assert(!$testArguments->hasGenerators());
}

#[Test]
function testTestArguments_HasGenerators_WhenGeneratorProvided_ReturnsTrue(): void
{
    $generator = (function () {
        yield 1;
    })();
    
    $testArguments = new TestArguments([1, $generator]);

    assert($testArguments->hasGenerators());
}

#[Test]
function testTestArguments_FindGenerator_WhenNoGeneratorProvided_ReturnsNull(): void
{
    $testArguments = new TestArguments([1]);

    $actual = $testArguments->findGenerator();

    assert(null === $actual, 'Expected: null. Actual: ' . var_export($actual, true));
}

#[Test]
function testTestArguments_FindGenerator_WhenGeneratorProvided_ReturnsMatchingIndex(): void
{
    $generator = (function () {
        yield 2;
    })();
    $testArguments = new TestArguments([1, $generator]);

    $actual = $testArguments->findGenerator();

    assert(1 === $actual, 'Expected: 1. Actual: ' . var_export($actual, true));
}

#[Fixture]
function exampleFixture(): int {
    return 1;
}

