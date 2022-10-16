<?php declare(strict_types=1);

namespace EasyTest;

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
				foreach ($this->testSuite->getSetups() as $setupFunction) {
					$setupFunction();
				}
				$function(...$prepared_arguments);
				foreach ($this->testSuite->getTearDowns() as $teardownFunction) {
					$teardownFunction();
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
