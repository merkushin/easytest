<?php declare(strict_types=1);

namespace EasyTest;

use Generator;

class TestSuiteRunner {

	private TestSuite $testSuite;

	private int $passes = 0;

	private array $failures = [];

	private array $errors = [];

	public function __construct(TestSuite $testSuite) {
		$this->testSuite = $testSuite;
	}

	public function run() {
		$this->passes = 0;
		$this->failures = [];
		$this->errors = [];

		foreach ($this->testSuite->getTests() as $test) {
			$function = $test['function'];
			$arguments = $test['arguments'];

			$prepared_arguments = [];
			foreach ($arguments as $name => $type) {
				try {
					if ($this->testSuite->hasFixture($name, $type)) {
						$prepared_arguments[] = $name();
					} else {
						$this->errors[] = new \RuntimeException("Didn't find fixture for $name");
						continue 2;
					}
				} catch (\Throwable $e) {
					$this->errors[] = new \RuntimeException("Fixture for $name failed: " . $e->getMessage());
					continue 2;
				}
			}

			try {
				if ($this->hasGenerators($prepared_arguments)) {
					$this->runGenerator($function, $prepared_arguments);
				} else {
					$this->runFunction($function, $prepared_arguments);
				}
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

	private function hasGenerators($arguments): bool {
		foreach ($arguments as $argument) {
			if ($argument instanceof Generator) {
				return true;
			}
		}
		return false;
	}

	private function runGenerator($function, $arguments) {
		$generatorInArgumens = $this->findGenerator($arguments);
		$generator = $arguments[$generatorInArgumens];
		if (!$generator instanceof Generator) {
			throw new \RuntimeException("Test function $function did not return a generator");
		}
		foreach ($generator as $value) {
			$arguments[$generatorInArgumens] = $value;
			$this->runFunction($function, $arguments);
		}
	}

	private function findGenerator($arguments): ?int {
		foreach ($arguments as $key => $argument) {
			if ($argument instanceof Generator) {
				return $key;
			}
		}
		return null;
	}

	private function runFunction($function, $arguments) {
		foreach ($this->testSuite->getSetups() as $setupFunction) {
			$setupFunction();
		}
		$function(...$arguments);
		foreach ($this->testSuite->getTearDowns() as $teardownFunction) {
			$teardownFunction();
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
