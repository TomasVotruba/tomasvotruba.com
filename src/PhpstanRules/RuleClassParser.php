<?php

declare(strict_types=1);

namespace App\PhpstanRules;

use App\ValueObject\ParsedRuleClass;
use SplFileInfo;

final class RuleClassParser
{
    public function parse(SplFileInfo $file, ?string $packageDir = null): ?ParsedRuleClass
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
            identifier: $this->extractIdentifier($contents, $packageDir),
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

    private function extractNodeType(string $contents): string
    {
        if (! preg_match('/@implements\s+(?:[A-Za-z\\\\]+)?Rule<([^>]+)>/', $contents, $m)) {
            return '';
        }

        $type = ltrim(trim($m[1]), '\\');
        // PhpParser\Node\... -> Node\...
        return (string) preg_replace('/^PhpParser\\\\/', '', $type);
    }

    /**
     * The PHPStan error identifier, e.g. "symplify.noDynamicName".
     * Handles both literal ->identifier('foo.bar') and constant ->identifier(RuleIdentifier::FOO).
     */
    private function extractIdentifier(string $contents, ?string $packageDir): string
    {
        // ->identifier('foo.bar') literal
        if (preg_match('/->identifier\(\s*([\'"])([\w.]+)\1/', $contents, $m)) {
            return $m[2];
        }

        // ->identifier(SomeClass::CONST) or ->identifier(self::CONST)
        if (! preg_match('/->identifier\(\s*([A-Za-z_\\\\]+)::(\w+)/', $contents, $m)) {
            return '';
        }

        [$reference, $constName] = [$m[1], $m[2]];

        $resolved = $this->resolveConstantValue($contents, $reference, $constName, $packageDir);
        if ($resolved !== '') {
            return $resolved;
        }

        // fallback: humanize the constant name (NO_DYNAMIC_NAME -> "no dynamic name")
        return strtolower(str_replace('_', ' ', $constName));
    }

    /**
     * Best-effort resolution of `ClassName::CONST` to its string value by locating
     * the defining class file inside the same package.
     */
    private function resolveConstantValue(
        string $contents,
        string $reference,
        string $constName,
        ?string $packageDir
    ): string {
        if ($packageDir === null) {
            return '';
        }

        // self::CONST / static::CONST resolve within the current file
        if (in_array($reference, ['self', 'static'], true)) {
            return $this->readConstantFromSource($contents, $constName);
        }

        $shortClass = $this->shortClassName($reference);

        foreach ($this->findFilesNamed($packageDir, $shortClass . '.php') as $candidate) {
            $value = $this->readConstantFromSource((string) file_get_contents($candidate), $constName);
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    private function readConstantFromSource(string $source, string $constName): string
    {
        if (preg_match('/const\s+' . preg_quote($constName, '/') . '\s*=\s*([\'"])([\w.]+)\1/', $source, $m)) {
            return $m[2];
        }

        return '';
    }

    private function shortClassName(string $reference): string
    {
        $parts = explode('\\', $reference);
        return (string) end($parts);
    }

    /**
     * @return list<string>
     */
    private function findFilesNamed(string $packageDir, string $filename): array
    {
        $found = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($packageDir, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $fileInfo) {
            if ($fileInfo instanceof SplFileInfo && $fileInfo->getFilename() === $filename) {
                $found[] = $fileInfo->getPathname();
            }
        }
        return $found;
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
