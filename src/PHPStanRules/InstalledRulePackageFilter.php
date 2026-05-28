<?php

declare(strict_types=1);

namespace App\PHPStanRules;

use App\PHPStanRules\ValueObject\PHPStanRulePackage;

final class InstalledRulePackageFilter
{
    /**
     * @param array<string, PHPStanRulePackage> $packages
     * @return list<PHPStanRulePackage>
     */
    public function filterInstalled(array $packages): array
    {
        $vendorRoot = base_path('vendor');

        return array_values(array_filter(
            $packages,
            static fn (PHPStanRulePackage $phpStanRulePackage): bool => is_dir(
                $vendorRoot . '/' . $phpStanRulePackage->getPackage()
            ),
        ));
    }
}
