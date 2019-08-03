<?php declare(strict_types=1);

namespace TomasVotruba\Website\Result;

use Nette\Utils\Strings;
use TomasVotruba\Website\Packagist\MinorPackageVersionsDownloadsProvider;
use TomasVotruba\Website\Packagist\PackageVersionPublishDatesProvider;

final class PackageDataGroupedByVersionFactory
{
    /**
     * @var MinorPackageVersionsDownloadsProvider
     */
    private $minorPackageVersionsDownloadsProvider;

    /**
     * @var PackageVersionPublishDatesProvider
     */
    private $packageVersionPublishDatesProvider;

    public function __construct(
        MinorPackageVersionsDownloadsProvider $minorPackageVersionsDownloadsProvider,
        PackageVersionPublishDatesProvider $packageVersionPublishDatesProvider
    ) {
        $this->minorPackageVersionsDownloadsProvider = $minorPackageVersionsDownloadsProvider;
        $this->packageVersionPublishDatesProvider = $packageVersionPublishDatesProvider;
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

            // complete relative number of downloads
            $totalDownloads = array_sum($packageDownloads[MinorPackageVersionsDownloadsProvider::DOWNLOADS_MINOR]);
            foreach ($packageDownloads[MinorPackageVersionsDownloadsProvider::DOWNLOADS_MINOR] as $version => $absoluteDownloads) {
                $relativeRate = 100 * ($absoluteDownloads/$totalDownloads);

                $packageDownloads[MinorPackageVersionsDownloadsProvider::DOWNLOADS_MINOR][$version] = [
                    'absolute_downloads' => $absoluteDownloads,
                    'relative_downloads' => round($relativeRate, 1),
                ];
            }

            $packagesData[$packageKey]['package_name'] = $packageName;

            $packagesData[$packageKey]['version_publish_dates'] = $this->packageVersionPublishDatesProvider->provideForPackage(
                $packageName
            );
        }

        return $packagesData;
    }

    private function createPackageKey(string $packageName): string
    {
        return Strings::replace($packageName, '#(/|-)#', '_');
    }
}
