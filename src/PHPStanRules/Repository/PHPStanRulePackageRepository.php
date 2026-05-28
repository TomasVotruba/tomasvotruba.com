<?php

declare(strict_types=1);

namespace App\PHPStanRules\Repository;

use App\PHPStanRules\ValueObject\PHPStanRulePackage;
use RuntimeException;

final class PHPStanRulePackageRepository
{
    private const string JSON_FILENAME = 'discover-phpstan-rule-repositories.json';

    /**
     * @var array<string, PHPStanRulePackage>|null
     */
    private ?array $cache = null;

    /**
     * Keyed by composer package name (e.g. "shipmonk/phpstan-rules").
     *
     * @return array<string, PHPStanRulePackage>
     */
    public function fetchAllByPackage(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        $path = resource_path(self::JSON_FILENAME);
        if (! is_file($path)) {
            throw new RuntimeException(sprintf('Repository metadata file %s is missing.', $path));
        }

        $payload = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

        $packages = [];
        foreach ($payload['repositories'] ?? [] as $entry) {
            $package = (string) ($entry['package'] ?? '');
            if ($package === '') {
                continue;
            }

            $packages[$package] = new PHPStanRulePackage(
                package: $package,
                group: (string) ($entry['group'] ?? ''),
                description: (string) ($entry['description'] ?? ''),
                url: (string) ($entry['url'] ?? ''),
            );
        }

        return $this->cache = $packages;
    }
}
