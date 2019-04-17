<?php declare(strict_types=1);

namespace TomasVotruba\Website\Result;

use Nette\Utils\Strings;
use TomasVotruba\Website\ArrayUtils;
use TomasVotruba\Website\Packagist\PackageMonthlyDownloadsProvider;
use TomasVotruba\Website\Statistics;

final class PackageDataFactory
{
    /**
     * @var PackageMonthlyDownloadsProvider
     */
    private $packageMonthlyDownloadsProvider;

    /**
     * @var Statistics
     */
    private $statistics;

    /**
     * @var ArrayUtils
     */
    private $arrayUtils;

    public function __construct(
        PackageMonthlyDownloadsProvider $packageMonthlyDownloadsProvider,
        Statistics $statistics,
        ArrayUtils $arrayUtils
    ) {
        $this->packageMonthlyDownloadsProvider = $packageMonthlyDownloadsProvider;
        $this->statistics = $statistics;
        $this->arrayUtils = $arrayUtils;
    }

    public function createPackagesData(array $packageNames): array
    {
        $packagesData = [];

        foreach ($packageNames as $packageName => $humanName) {
            $monthlyDownloads = $this->packageMonthlyDownloadsProvider->provideForPackage($packageName);

            // no data
            if (! isset($monthlyDownloads[0])) {
                continue;
            }

            $lastMonthDailyDownloads = $monthlyDownloads[0];

            // too small package → skip it
            if ($lastMonthDailyDownloads <= 1000) {
                continue;
            }

            // package younger than 12 months → skip it
            if (! isset($monthlyDownloads[11])) {
                continue;
            }

            $lastYearTrend = $this->statistics->resolveTrend($packageName, $monthlyDownloads, 12);
            if ($lastYearTrend === null) {
                continue;
            }

            // too fresh package → skip it
            if ($lastYearTrend > 300) {
                continue;
            }

            $packageData = [
                'package_name' => $packageName,
                'short_name' => $humanName,
                'last_month_average_daily_downloads' => $lastMonthDailyDownloads,
                'last_year_trend' => $lastYearTrend,
                'last_year_total' => $this->statistics->resolveTotal($monthlyDownloads, 12),
            ];

            $packageKey = $this->createPackageKey($packageName);
            $packagesData[$packageKey] = $packageData;
        }

        return $this->arrayUtils->sortDataByKey($packagesData, 'last_year_trend');
    }

    private function createPackageKey(string $packageName): string
    {
        return Strings::replace($packageName, '#(/|-)#', '_');
    }
}
