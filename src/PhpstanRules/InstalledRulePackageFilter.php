<?php

declare(strict_types=1);

namespace App\PhpstanRules;

use App\ValueObject\PhpstanRulePackage;

final class InstalledRulePackageFilter
{
    /**
     * @param array<string, PhpstanRulePackage> $packages
     * @return list<PhpstanRulePackage>
     */
    public function filterInstalled(array $packages): array
    {
        $vendorRoot = base_path('vendor');

        return array_values(array_filter(
            $packages,
            static fn (PhpstanRulePackage $package): bool => is_dir($vendorRoot . '/' . $package->getPackage()),
        ));
    }
}
