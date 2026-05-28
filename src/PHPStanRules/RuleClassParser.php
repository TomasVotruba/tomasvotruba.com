<?php

declare(strict_types=1);

namespace App\PHPStanRules;

use App\PHPStanRules\ValueObject\ParsedRuleClass;
use SplFileInfo;

final class RuleClassParser
{
    public function parse(SplFileInfo $file): ?ParsedRuleClass
    {
        $contents = (string) file_get_contents($file->getPathname());

        // strip /* ... */ block + // line comments so docblock text never matches our class regex
        $stripped = preg_replace('!/\*.*?\*/!s', '', $contents) ?? $contents;
        $stripped = preg_replace('!//[^\n]*!', '', $stripped) ?? $stripped;

        // line-anchored class declaration that "implements ... Rule"
        if (! preg_match(
            '/^\s*(?:final\s+)?(?:abstract\s+)?class\s+(\w+)\b[^{]*\bimplements\b[^{]*\bRule\b/m',
            $stripped,
            $classMatch
        )) {
            return null;
        }

        $shortName = $classMatch[1];

        if (! preg_match('/^namespace\s+([^;]+);/m', $stripped, $nsMatch)) {
            return null;
        }

        $fullyQualifiedName = trim($nsMatch[1]) . '\\' . $shortName;

        return new ParsedRuleClass(
            shortName: $shortName,
            fullyQualifiedName: $fullyQualifiedName,
            message: $this->extractMessage($contents),
            tip: $this->extractTip($contents),
            docComment: $this->extractDocComment($contents, $shortName),
        );
    }

    private function extractMessage(string $contents): string
    {
        // const ERROR_MESSAGE = '...';
        if (preg_match('/const\s+ERROR_MESSAGE\s*=\s*([\'"])(.+?)\1\s*;/s', $contents, $m)) {
            return $this->normalizeWhitespace($m[2]);
        }

        // RuleErrorBuilder::message('...')
        if (preg_match('/RuleErrorBuilder::message\(\s*([\'"])(.+?)\1/s', $contents, $m)) {
            return $this->normalizeWhitespace($m[2]);
        }

        // sprintf('...') inside message(...)
        if (preg_match('/RuleErrorBuilder::message\(\s*\\\\?sprintf\(\s*([\'"])(.+?)\1/s', $contents, $m)) {
            return $this->normalizeWhitespace($m[2]);
        }

        return '';
    }

    private function extractTip(string $contents): string
    {
        if (preg_match('/->tip\(\s*([\'"])(.+?)\1/s', $contents, $m)) {
            return $this->normalizeWhitespace($m[2]);
        }

        return '';
    }

    /**
     * Prose summary from the class-level docblock (skips @-tags and {@inheritdoc}).
     */
    private function extractDocComment(string $contents, string $shortName): string
    {
        $pattern = '/\/\*\*(.*?)\*\/\s*(?:#\[[^\]]*\]\s*)*(?:final\s+)?(?:abstract\s+)?class\s+'
            . preg_quote($shortName, '/') . '\b/s';
        if (! preg_match($pattern, $contents, $m)) {
            return '';
        }

        $lines = preg_split('/\r?\n/', $m[1]) ?: [];
        $summary = [];
        foreach ($lines as $line) {
            $line = trim($line);
            $line = ltrim($line, '*');
            $line = trim($line);
            if ($line === '') {
                if ($summary !== []) {
                    break;
                }

                continue;
            }

            if (str_starts_with($line, '@') || str_starts_with($line, '{@')) {
                continue;
            }

            $summary[] = $line;
        }

        return $this->normalizeWhitespace(implode(' ', $summary));
    }

    private function normalizeWhitespace(string $raw): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $raw);
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;
        return trim($text);
    }
}
