<?php declare(strict_types=1);

namespace EasyTest;

use Generator;

class TestArguments {
	public function __construct(
		private array $arguments
	) {}

	public function getPrepared(): array {
		return $this->arguments;
	}
	
	public function hasGenerators(): bool {
		foreach ($this->arguments as $argument) {
			if ($argument instanceof Generator) {
				return true;
			}
		}
		return false;
	}

	public function findGenerator(): ?int {
		foreach ($this->arguments as $key => $argument) {
			if ($argument instanceof Generator) {
				return $key;
			}
		}
		return null;
	}

	public static function prepare(TestSuiteInterface $testSuite, array $arguments): self {
		$preparedArguments = [];
		foreach ($arguments as $name => $type) {
			try {
				if ($testSuite->hasFixture($name, $type)) {
					$preparedArguments[] = $name();
				} else {
					throw new \RuntimeException("Didn't find fixture for $name");
				}
			} catch (\Throwable $e) {
				throw new \RuntimeException("Fixture for $name failed: " . $e->getMessage());
			}
		}
		
		return new self($preparedArguments);
	}
}
