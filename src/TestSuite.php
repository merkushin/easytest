<?php declare(strict_types=1);

namespace PhpEasyest;

use PhpEasyest\Attribute\Fixture;
use PhpEasyest\Attribute\Ignore;
use PhpEasyest\Attribute\Setup;
use PhpEasyest\Attribute\TearDown;
use PhpEasyest\Attribute\Test;


class TestSuite {

	private array $functionNames;

	private array $tests;
	private array $fixtures;
	private array $setups;
	private array $tearDowns;

	public function __construct(array $functionNames) {
		$this->functionNames = $functionNames;
	}

	public function prepare(): void {
		$this->tests = [];
		$this->fixtures = [];
		$this->setups = [];
		$this->tearDowns = [];
		foreach ($this->functionNames as $function) {
			$this->inspectFunction($function);
		}
	}
	
	private function inspectFunction(string $function): void {
		$reflection = new \ReflectionFunction($function);

		$ignore = $reflection->getAttributes(Ignore::class);
		if (count($ignore) > 0) {
			return;
		}

		$arguments = [];
		foreach ($reflection->getParameters() as $parameter) {
			$paramNamespace = $reflection->getNamespaceName() ? $reflection->getNamespaceName() . '\\' : '';
			$arguments[$paramNamespace . $parameter->getName()] = $parameter->getType()->getName();
		}

		foreach ($reflection->getAttributes() as $attr) {
			$name = $attr->getName();
			if ($name === Fixture::class) {
				$this->fixtures[$reflection->getName()] = $reflection->getReturnType()->getName();
			} elseif ($name === Test::class) {
				$this->tests[] = [
					'function' => $reflection->getName(),
					'arguments' => $arguments,
				];
			} elseif ($name === Setup::class) {
				$this->setups[] = $reflection->getName();
			} elseif ($name === TearDown::class) {
				$this->tearDowns[] = $reflection->getName();
			}
		}
	}

	public function getTests(): array {
		return $this->tests;
	}

	public function hasFixture(string $fixtureName, string $fixtureType): bool {
		return isset($this->fixtures[$fixtureName]) && $this->fixtures[$fixtureName] === $fixtureType;
	}

	public function getSetups(): array {
		return $this->setups;
	}

	public function getTearDowns(): array {
		return $this->tearDowns;
	}
}
