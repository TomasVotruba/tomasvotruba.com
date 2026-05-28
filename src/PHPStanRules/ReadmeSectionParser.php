<?php

declare(strict_types=1);

namespace App\PHPStanRules;

final class ReadmeSectionParser
{
    /**
     * @var list<string>
     */
    private const array README_FILENAMES = ['README.md', 'Readme.md', 'readme.md'];

    /**
     * Returns a map of normalized-heading => section-body.
     *
     * @return array<string, string>
     */
    public function parseSections(string $packageDir): array
    {
        $readme = $this->loadReadme($packageDir);
        if ($readme === '') {
            return [];
        }

        // headings level 3-6 (### through ######)
        if (! preg_match_all('/^(#{3,6})\s+(.+?)\s*$/m', $readme, $matches, PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $sections = [];
        $count = count($matches[0]);
        for ($i = 0; $i < $count; $i++) {
            $headingRaw = $matches[2][$i][0];
            $start = $matches[0][$i][1] + strlen($matches[0][$i][0]);
            $end = $i + 1 < $count
                ? $matches[0][$i + 1][1]
                : strlen($readme);
            $body = substr($readme, $start, $end - $start);

            // last write wins is fine; some packages duplicate sub-headings
            $sections[$this->normalizeHeading($headingRaw)] = $body;
        }

        return $sections;
    }

    private function loadReadme(string $packageDir): string
    {
        foreach (self::README_FILENAMES as $name) {
            $path = $packageDir . '/' . $name;
            if (is_file($path)) {
                return (string) file_get_contents($path);
            }
        }

        return '';
    }

    private function normalizeHeading(string $heading): string
    {
        // strip backticks, code formatting, leading namespace pieces
        $h = trim($heading, " \t`*");
        // remove trailing markers like " *" or " (deprecated)"
        $h = preg_replace('/\s*\*+\s*$/', '', $h) ?? $h;
        // take last path segment (Classes\FinalRule -> FinalRule)
        $parts = preg_split('/[\\\\\/]/', $h);
        if ($parts === false || $parts === []) {
            return strtolower($h);
        }

        return strtolower((string) end($parts));
    }
}
