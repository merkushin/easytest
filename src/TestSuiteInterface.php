<?php declare(strict_types=1);

namespace EasyTest;

interface TestSuiteInterface {
	public function prepare(): void;
	public function getTests(): array;
	public function hasFixture(string $fixtureName, string $fixtureType): bool;
	public function getSetups(): array;
	public function getTearDowns(): array; 
}
