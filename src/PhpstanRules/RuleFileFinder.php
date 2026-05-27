<?php

declare(strict_types=1);

namespace App\PhpstanRules;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class RuleFileFinder
{
    /**
     * @var list<string>
     */
    private const array SOURCE_DIRS = ['src', 'lib'];

    /**
     * @return iterable<SplFileInfo>
     */
    public function findInPackage(string $packageDir): iterable
    {
        foreach (self::SOURCE_DIRS as $sourceDir) {
            $path = $packageDir . '/' . $sourceDir;
            if (! is_dir($path)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
            foreach ($iterator as $file) {
                if (! $file instanceof SplFileInfo) {
                    continue;
                }
                if ($file->getExtension() !== 'php') {
                    continue;
                }
                yield $file;
            }
        }
    }
}
