<?php declare(strict_types=1);

namespace PhpEasyest;

class ArgumentParser {
	private ?string $testsPath;
	private ?string $bootstrapPath;

	public function parse(array $arguments): void {
		$this->testsPath = $arguments[1] ?? null;
		$this->bootstrapPath = $arguments[2] ?? null;
	}
	
	public function getTestsPath(string $default = './tests'): string {
		return $this->testsPath ?? $default;
	}

	public function getBootstrapPath(string $default = './vendor/autoload.php'): string {
		return $this->bootstrapPath ?? $default;
	}
}

