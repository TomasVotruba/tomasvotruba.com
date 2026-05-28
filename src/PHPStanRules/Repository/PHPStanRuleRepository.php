<?php

declare(strict_types=1);

namespace App\PHPStanRules\Repository;

use App\PHPStanRules\ValueObject\PHPStanRule;
use RuntimeException;

final class PHPStanRuleRepository
{
    private const string JSON_FILENAME = 'discover-phpstan-rules.json';

    private const int MIN_RULES_PER_PACKAGE = 3;

    /**
     * @var PHPStanRule[]|null
     */
    private ?array $cache = null;

    /**
     * @return PHPStanRule[]
     */
    public function fetchAll(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        $path = resource_path(self::JSON_FILENAME);
        if (! is_file($path)) {
            throw new RuntimeException(sprintf(
                'Rule data file %s is missing. Run `php artisan app:scan-phpstan-rules` to generate it.',
                $path
            ));
        }

        $payload = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

        $rules = [];
        foreach ($payload['rules'] ?? [] as $entry) {
            $rules[] = new PHPStanRule(
                group: (string) ($entry['group'] ?? ''),
                package: (string) ($entry['package'] ?? ''),
                class: (string) ($entry['class'] ?? ''),
                name: (string) ($entry['name'] ?? ''),
                message: (string) ($entry['message'] ?? ''),
                description: (string) ($entry['description'] ?? ''),
                wrongCode: (string) ($entry['wrong_code'] ?? ''),
                correctCode: (string) ($entry['correct_code'] ?? ''),
                tip: (string) ($entry['tip'] ?? ''),
            );
        }

        return $this->cache = $rules;
    }

    /**
     * @return array<string, PHPStanRule[]>
     */
    public function fetchGroupedByPackage(): array
    {
        $grouped = [];
        foreach ($this->fetchAll() as $phpStanRule) {
            $grouped[$phpStanRule->getPackage()][] = $phpStanRule;
        }

        $grouped = array_filter(
            $grouped,
            static fn (array $rules): bool => count($rules) >= self::MIN_RULES_PER_PACKAGE,
        );
        uksort(
            $grouped,
            static fn (string $a, string $b): int => count($grouped[$b]) <=> count($grouped[$a]) ?: $a <=> $b
        );
        return $grouped;
    }

    public function getGeneratedAt(): ?string
    {
        $path = resource_path(self::JSON_FILENAME);
        if (! is_file($path)) {
            return null;
        }

        $payload = json_decode((string) file_get_contents($path), true);
        return $payload['generated_at'] ?? null;
    }
}
