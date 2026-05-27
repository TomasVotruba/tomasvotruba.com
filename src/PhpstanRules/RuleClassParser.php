<?php

declare(strict_types=1);

namespace App\PhpstanRules;

use App\ValueObject\ParsedRuleClass;
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
            nodeType: $this->extractNodeType($contents),
        );
    }

    private function extractMessage(string $contents): string
    {
        // const ERROR_MESSAGE = '...';
        if (preg_match('/const\s+ERROR_MESSAGE\s*=\s*([\'"])(.+?)\1\s*;/s', $contents, $m)) {
            return $this->normalizeMessage($m[2]);
        }

        // RuleErrorBuilder::message('...')
        if (preg_match('/RuleErrorBuilder::message\(\s*([\'"])(.+?)\1/s', $contents, $m)) {
            return $this->normalizeMessage($m[2]);
        }

        // sprintf('...') inside message(...)
        if (preg_match('/RuleErrorBuilder::message\(\s*\\\\?sprintf\(\s*([\'"])(.+?)\1/s', $contents, $m)) {
            return $this->normalizeMessage($m[2]);
        }

        return '';
    }

    private function extractNodeType(string $contents): string
    {
        if (! preg_match('/@implements\s+(?:[A-Za-z\\\\]+)?Rule<([^>]+)>/', $contents, $m)) {
            return '';
        }

        $type = ltrim(trim($m[1]), '\\');
        // PhpParser\Node\... -> Node\...
        return (string) preg_replace('/^PhpParser\\\\/', '', $type);
    }

    private function normalizeMessage(string $raw): string
    {
        $msg = str_replace(["\r\n", "\r"], "\n", $raw);
        $msg = preg_replace('/\s+/', ' ', $msg) ?? $msg;
        return trim($msg);
    }
}
