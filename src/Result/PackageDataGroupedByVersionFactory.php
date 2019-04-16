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

            $packageDownloads = $this->minorPackageVersionsDownloadsProvider->provideForPackage($packageName);
            if ($packageDownloads === []) {
                continue;
            }

            $packagesData[$packageKey] = $packageDownloads;
            $packagesData[$packageKey]['adoption_rate'] = $this->resolveAdoptionRate($packageDownloads);
            $packagesData[$packageKey]['package_name'] = $packageName;
        }

        return $packagesData;
    }

    private function createPackageKey(string $packageName): string
    {
        return Strings::replace($packageName, '#(/|-)#', '_');
    }

    private function resolveAdoptionRate(array $packageDownloads): float
    {
        $downloadsTotal = array_sum($packageDownloads['downloads_minor']);

        $lastVersionDownloads = array_shift($packageDownloads['downloads_minor']);
        $adoption_rate = $lastVersionDownloads / $downloadsTotal * 100;

        return (float) round($adoption_rate, 1);
    }
}
