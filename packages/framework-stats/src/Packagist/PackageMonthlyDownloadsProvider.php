<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Packagist;

use TomasVotruba\FrameworkStats\Packagist\Purifier\InterveningPackagesPurifier;

final class PackageMonthlyDownloadsProvider
{
    private PackageRawMonthlyDownloadsProvider $packageRawMonthlyDownloadsProvider;

    private InterveningPackagesPurifier $interveningPackagesPurifier;

    public function __construct(
        PackageRawMonthlyDownloadsProvider $packageRawMonthlyDownloadsProvider,
        InterveningPackagesPurifier $interveningPackagesPurifier
    ) {
        $this->packageRawMonthlyDownloadsProvider = $packageRawMonthlyDownloadsProvider;
        $this->interveningPackagesPurifier = $interveningPackagesPurifier;
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
