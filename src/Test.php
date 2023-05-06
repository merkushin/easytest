<?php declare(strict_types=1);

namespace EasyTest;

use Generator;

class Test {

	public function __construct(
		private TestSuiteInterface $testSuite,
		private string $function,
		private array $arguments,
	) {}

	public function run() {
		$testArguments = TestArguments::prepare($this->testSuite, $this->arguments);
		if ($testArguments->hasGenerators()) {
			$this->runWithGenerator($testArguments);
		} else {
			$this->runFunction($testArguments->getPrepared());
		}
	}

	private function runWithGenerator(TestArguments $arguments) {
		$generatorInArgumens = $arguments->findGenerator();
		$preparedArguments = $arguments->getPrepared();
		$generator = $preparedArguments[$generatorInArgumens];
		if (!$generator instanceof Generator) {
			throw new \RuntimeException("Fixture for {$this->function} did not return an expected generator");
		}

		foreach ($generator as $value) {
			$preparedArguments[$generatorInArgumens] = $value;
			$this->runFunction($preparedArguments);
		}
	}

	private function runFunction(array $arguments) {
		foreach ($this->testSuite->getSetups() as $setupFunction) {
			$setupFunction();
		}
		call_user_func_array($this->function, $arguments);
		foreach ($this->testSuite->getTearDowns() as $teardownFunction) {
			$teardownFunction();
		}
	}
}
