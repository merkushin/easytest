<?php declare(strict_types=1);

namespace PhpEasyest;

use PhpEasyest\Attribute\Fixture;
use PhpEasyest\Attribute\Ignore;
use PhpEasyest\Attribute\Setup;
use PhpEasyest\Attribute\TearDown;
use PhpEasyest\Attribute\Test;
use PhpEasyest\FileFinder;

class Runner {
	private int $passes = 0;
	private array $failures = [];
	private array $errors = [];

	public function run(string $testDirectory, string $bootstrapScript): void {
		echo "Running tests in $testDirectory\n";
		echo "Using $bootstrapScript\n";
		require $bootstrapScript;
		
		$fileFinder = new FileFinder();
		foreach ($fileFinder->findIn($testDirectory) as $filePath) {
			$funcs = get_defined_functions()["user"];
			require_once $filePath;
			$definedFunctions = array_values(array_diff(get_defined_functions()["user"], $funcs));
			$this->runTests($definedFunctions);
		}

		echo "\n";

		$resultFormatter = new ResultFormatter();
		echo $resultFormatter->format($this->passes, $this->failures, $this->errors);
	}

	private function runTests(array $definedFunctions): void {
		$testSuite = new TestSuite($definedFunctions);
		$testSuite->prepare();

		$testSuiteRunner = new TestSuiteRunner($testSuite);
		foreach ($testSuiteRunner->run() as $testResult) {
			echo $testResult;
		}

		$this->passes += $testSuiteRunner->getPasses();
		$this->failures = array_merge($this->failures, $testSuiteRunner->getFailures());
		$this->errors = array_merge($this->errors, $testSuiteRunner->getErrors());
	}
}
