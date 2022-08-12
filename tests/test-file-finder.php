<?php declare(strict_types=1);

namespace PhpEasyestTest\FileCollector;

use PhpEasyest\Attribute\Test;
use PhpEasyest\Attribute\Fixture;
use PhpEasyest\FileFinder;

#[Fixture]
function fileFinder(): FileFinder {
	return new FileFinder();
}

#[Test]
function testFileFinder(FileFinder $fileFinder): void
{
	$dirname = dirname(__FILE__) . '/test-file-finder';
	$expected = [
		$dirname . '/1.php',
		$dirname . '/2.php',
	];

	$generator = $fileFinder->findIn($dirname);
	$actual = iterator_to_array($generator);

	assert($actual === $expected);
}

