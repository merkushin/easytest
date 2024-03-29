<?php declare(strict_types=1);

namespace EasyTest;

class FileFinder {
    public function findIn(string $path) {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            yield $file->getPathname();
        }
    }
}
