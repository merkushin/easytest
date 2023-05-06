<?php declare(strict_types=1);

namespace EasyTest;

use EasyTest\Attribute\Fixture;
use EasyTest\Attribute\Ignore;
use EasyTest\Attribute\Setup;
use EasyTest\Attribute\TearDown;
use EasyTest\Attribute\Test as TestAttribute;
use Generator;

final class TestSuite implements TestSuiteInterface {

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
			switch ($attr->getName()) {
				case Fixture::class:
					$this->fixtures[$reflection->getName()] = $reflection->getReturnType()->getName();
					break;
				case TestAttribute::class:
					$this->tests[] = new Test($this, $reflection->getName(), $arguments);
					break;
				case Setup::class:
					$this->setups[] = $reflection->getName();
					break;
				case TearDown::class:
					$this->tearDowns[] = $reflection->getName();
					break;
			}
		}
	}

	public function getTests(): array {
		return $this->tests;
	}

	public function hasFixture(string $fixtureName, string $fixtureType): bool {
		if (!isset($this->fixtures[$fixtureName])) {
			return false;
		}

		return $this->fixtures[$fixtureName] === $fixtureType || $this->fixtures[$fixtureName] === Generator::class;
	}

	public function getSetups(): array {
		return $this->setups;
	}

	public function getTearDowns(): array {
		return $this->tearDowns;
	}
}
