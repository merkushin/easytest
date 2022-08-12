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

		if (count($this->failures) === 0 && count($this->errors) === 0) {
			echo "\nAll tests passed.\n";
			return;
		} 

		echo "\nSome tests failed.\n";

		if (count($this->failures)) {
			echo "Failed tests:\n";
			foreach ($this->failures as  $i => $fail) {
				echo " - [" . ($i + 1) . "] " . $fail->getMessage() . "\n";
				echo $fail->getTraceAsString() . "\n\n";
			}
		}
		if (count($this->errors)) {
			echo "Errors:\n";
			foreach ($this->errors as  $i => $error) {
				echo " - [" . ($i + 1) . "] " . $error->getMessage() . "\n";
				echo $error->getTraceAsString() . "\n\n";
			}
		}
	}

	public function runTests(array $definedFunctions): void {

		$fixtures = [];
		$tests = [];
		$setup = [];
		$teardown = [];


		foreach ($definedFunctions as $function) {
			$reflection = new \ReflectionFunction($function);

			$ignore = $reflection->getAttributes(Ignore::class);
			if (count($ignore) > 0) {
				continue;
			}

			$arguments = [];
			foreach ($reflection->getParameters() as $parameter) {
				$paramNamespace = $reflection->getNamespaceName() ? $reflection->getNamespaceName() . '\\' : '';
				$arguments[$paramNamespace . $parameter->getName()] = $parameter->getType()->getName();
			}


			foreach ($reflection->getAttributes() as $attr) {
				$name = $attr->getName();
				if ($name === Fixture::class) {
					$fixtures[$reflection->getName()] = $reflection->getReturnType()->getName();
				} elseif ($name === Test::class) {
					$tests[] = [
						'function' => $reflection->getName(),
						'arguments' => $arguments,
					];
				} elseif ($name === Setup::class) {
					$setup[] = $reflection->getName();
				} elseif ($name === TearDown::class) {
					$teardown[] = $reflection->getName();
				}
			}

		}

		foreach ($tests as $test) {
			$function = $test['function'];
			$arguments = $test['arguments'];

			$prepared_arguments = [];
			foreach ($arguments as $name => $type) {
				try {
					if (isset($fixtures[$name]) && $fixtures[$name] === $type) {
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
				foreach ($setup as $setupFunction) {
					$setupFunction();
				}
				$function(...$prepared_arguments);
				foreach ($teardown as $teardownFunction) {
					$teardownFunction();
				}
				$this->passes++;
				echo '.';
			} catch (\AssertionError $e) {
				$this->failures[] = $e;
				echo 'F';
			} catch (\Throwable $e) {
				$this->errors[] = $e;
				echo 'E';
			}
		}
	}
}
