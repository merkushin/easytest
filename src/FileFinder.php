<?php declare(strict_types=1);

namespace PhpEasyest;

class FileFinder {
	public function findIn(string $path) {
		$files = [];
		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
		foreach ($iterator as $file) {
			if (!$file->isFile() || $file->getExtension() !== 'php') {
				continue;
			}
			yield $file->getPathname();
		}
	}
}
