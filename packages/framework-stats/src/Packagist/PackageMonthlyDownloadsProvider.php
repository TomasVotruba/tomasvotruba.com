<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Packagist;

use TomasVotruba\FrameworkStats\Packagist\Purifier\InterveningPackagesPurifier;

/**
 * @see \TomasVotruba\FrameworkStats\Tests\Packagist\PackageRawMonthlyDownloadsProviderTest
 */
final class PackageMonthlyDownloadsProvider
{
    public function __construct(
        private PackageRawMonthlyDownloadsProvider $packageRawMonthlyDownloadsProvider,
        private InterveningPackagesPurifier $interveningPackagesPurifier
    ) {
    }

    /**
     * @return int[]
     */
    public function provideForPackage(string $packageName): array
    {
        $values = $this->packageRawMonthlyDownloadsProvider->provideForPackage($packageName);

        return $this->interveningPackagesPurifier->correctInterveningPackages($values, $packageName);
    }
}
