<?php declare(strict_types=1);

namespace EasyTest;

class TestSuiteRunner {

    private TestSuiteInterface $testSuite;

    private int $passes = 0;

    private array $failures = [];

    private array $errors = [];

    public function __construct(TestSuiteInterface $testSuite) {
        $this->testSuite = $testSuite;
    }

    public function run() {
        $this->passes = 0;
        $this->failures = [];
        $this->errors = [];

        foreach ($this->testSuite->getTests() as $test) {
            try {
                $test->run();
                $this->passes++;
                yield '.';
            } catch (\AssertionError $e) {
                $this->failures[] = $e;
                yield 'F';
            } catch (\Throwable $e) {
                $this->errors[] = $e;
                yield 'E';
            }
        }
    }

    public function getPasses(): int {
        return $this->passes;
    }

    public function getFailures(): array {
        return $this->failures;
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
