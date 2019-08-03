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
            $totalDownloads = array_sum($packageDownloads);
            foreach ($packageDownloads as $version => $absoluteDownloads) {
                $packageDownloads[$version] = [
                    'absolute_downloads' => $absoluteDownloads,
                    'relative_downloads' => round($absoluteDownloads/$totalDownloads + 1, 1),
                ];
            }

            dump($packageDownloads);

            dump($packageDownloads);
            die;

            $packagesData[$packageKey]['adoption_rate'] = $this->resolveAdoptionRate($packageDownloads);
            $packagesData[$packageKey]['package_name'] = $packageName;

            $packagesData[$packageKey]['version_publish_dates'] = $this->packageVersionPublishDatesProvider->provideForPackage(
                $packageName
            );
        }

        return $this->sortPackagesByAdoptionRate($packagesData);
    }

    private function createPackageKey(string $packageName): string
    {
        return Strings::replace($packageName, '#(/|-)#', '_');
    }

    private function resolveAdoptionRate(array $packageDownloads): float
    {
        $downloadsTotal = array_sum($packageDownloads[MinorPackageVersionsDownloadsProvider::DOWNLOADS_MINOR]);

        $lastVersionDownloads = array_shift($packageDownloads[MinorPackageVersionsDownloadsProvider::DOWNLOADS_MINOR]);
        $adoption_rate = $lastVersionDownloads / $downloadsTotal * 100;

        return (float) round($adoption_rate, 1);
    }

    private function sortPackagesByAdoptionRate(array $packagesData): array
    {
        usort($packagesData, function (array $firstPackage, array $secondPackage): int {
            return $secondPackage['adoption_rate'] <=> $firstPackage['adoption_rate'];
        });

        return $packagesData;
    }
}
