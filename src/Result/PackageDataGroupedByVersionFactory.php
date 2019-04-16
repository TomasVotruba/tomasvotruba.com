<?php declare(strict_types=1);

namespace TomasVotruba\Website\Result;

use Nette\Utils\Strings;
use TomasVotruba\Website\Packagist\MinorPackageVersionsDownloadsProvider;

final class PackageDataGroupedByVersionFactory
{
    /**
     * @var MinorPackageVersionsDownloadsProvider
     */
    private $minorPackageVersionsDownloadsProvider;

    public function __construct(MinorPackageVersionsDownloadsProvider $minorPackageVersionsDownloadsProvider)
    {
        $this->minorPackageVersionsDownloadsProvider = $minorPackageVersionsDownloadsProvider;
    }

    /**
     * @param string[] $packageNames
     */
    public function createPackagesData(array $packageNames): array
    {
        $packagesData = [];

        foreach ($packageNames as $packageName) {
            $packageKey = $this->createPackageKey($packageName);
            $packagesData[$packageKey] = $this->minorPackageVersionsDownloadsProvider->provideForPackage($packageName);
        }

        return $packagesData;
    }

    private function createPackageKey(string $packageName): string
    {
        return Strings::replace($packageName, '#(/|-)#', '_');
    }
}
