<?php

declare(strict_types=1);

namespace App\PhpstanRules;

use App\ValueObject\RuleSnippets;

final class CodeSnippetExtractor
{
    /**
     * @param array<string, string> $sections
     */
    public function extractFor(string $shortName, array $sections): RuleSnippets
    {
        foreach ($this->headingCandidates($shortName) as $candidate) {
            if (! isset($sections[$candidate])) {
                continue;
            }

            $body = $sections[$candidate];
            $blocks = $this->extractCodeBlocks($body);

            return new RuleSnippets(
                description: $this->extractFirstParagraph($body),
                wrongCode: $blocks['wrong'],
                correctCode: $blocks['correct'],
            );
        }

        return RuleSnippets::empty();
    }

    /**
     * First non-empty markdown paragraph (skips list-of-headings, code fences, blank lines).
     */
    private function extractFirstParagraph(string $body): string
    {
        return $this->stripMarkdown(implode(' ', $this->collectFirstParagraphLines($body)));
    }

    /**
     * @return list<string>
     */
    private function collectFirstParagraphLines(string $body): array
    {
        $lines = preg_split('/\r?\n/', $body) ?: [];
        $paragraph = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($this->isParagraphBreak($trimmed)) {
                if ($paragraph !== []) {
                    break;
                }
                continue;
            }
            $paragraph[] = $trimmed;
        }
        return $paragraph;
    }

    private function isParagraphBreak(string $trimmedLine): bool
    {
        return $trimmedLine === ''
            || str_starts_with($trimmedLine, '#')
            || str_starts_with($trimmedLine, '```');
    }

    private function stripMarkdown(string $text): string
    {
        // [text](url) -> text
        $text = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $text) ?? $text;
        $text = str_replace('`', '', $text);
        // leading emoji shortcodes like :bulb:
        $text = preg_replace('/:[a-z_]+:\s*/i', '', $text) ?? $text;
        return trim($text);
    }

    /**
     * @return list<string>
     */
    private function headingCandidates(string $shortName): array
    {
        $stripped = preg_replace('/Rule$/', '', $shortName) ?? $shortName;

        $variants = [
            $shortName,
            $stripped,
            // camelCase variant for shipmonk-style headings (forbidCast etc.)
            lcfirst($stripped),
            lcfirst($shortName),
        ];

        return array_values(array_unique(array_map('strtolower', $variants)));
    }

    /**
     * Pull at most two fenced code blocks from a README section.
     *
     * - If a ```diff block exists, split its lines into "wrong" (-) and "correct" (+).
     * - Otherwise take the first ```php block as wrong and the second as correct.
     *   (neon/yaml etc are config, not example code)
     *
     * @return array{wrong: string, correct: string}
     */
    private function extractCodeBlocks(string $body): array
    {
        if (preg_match('/```diff\n(.+?)```/s', $body, $m)) {
            return $this->splitDiffBlock($m[1]);
        }

        return $this->firstTwoPhpBlocks($body);
    }

    /**
     * @return array{wrong: string, correct: string}
     */
    private function splitDiffBlock(string $diffBody): array
    {
        $wrong = [];
        $correct = [];
        foreach (preg_split('/\r?\n/', $diffBody) ?: [] as $line) {
            if (str_starts_with($line, '-') && ! str_starts_with($line, '--')) {
                $wrong[] = ' ' . substr($line, 1);
            } elseif (str_starts_with($line, '+') && ! str_starts_with($line, '++')) {
                $correct[] = ' ' . substr($line, 1);
            } else {
                $wrong[] = $line;
                $correct[] = $line;
            }
        }
        return [
            'wrong' => trim(implode("\n", $wrong)),
            'correct' => trim(implode("\n", $correct)),
        ];
    }

    /**
     * @return array{wrong: string, correct: string}
     */
    private function firstTwoPhpBlocks(string $body): array
    {
        if (! preg_match_all('/```php\n(.+?)```/s', $body, $matches)) {
            return [
                'wrong' => '',
                'correct' => '',
            ];
        }

        $blocks = array_map('trim', $matches[1]);
        return [
            'wrong' => $blocks[0] ?? '',
            'correct' => $blocks[1] ?? '',
        ];
    }
}
