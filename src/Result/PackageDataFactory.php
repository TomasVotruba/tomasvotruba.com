<?php declare(strict_types=1);

namespace TomasVotruba\Website\Result;

use Nette\Utils\Strings;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Website\ArrayUtils;
use TomasVotruba\Website\Packagist\PackageMonthlyDownloadsProvider;
use TomasVotruba\Website\Statistics;

final class PackageDataFactory
{
    /**
     * @var int
     */
    private const MAX_TREND_LIMIT = 300;
    /**
     * @var int
     */
    private const MIN_DOWNLOADS_LIMIT = 1000;

    /**
     * @var PackageMonthlyDownloadsProvider
     */
    private $packageMonthlyDownloadsProvider;

    /**
     * @var Statistics
     */
    private $statistics;

    /**
     * Packages that create no value, are empty or just util
     * @var string[]
     */
    private $pseudoPackages = [
        'symfony/apache-pack',
        'symfony/serializer-pack',
        'symfony/debug-pack',
        'symfony/profiler-pack',
        'symfony/orm-pack',
        'symfony/webpack-encore-pack',
    ];

    /**
     * @var ArrayUtils
     */
    private $arrayUtils;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(
        PackageMonthlyDownloadsProvider $packageMonthlyDownloadsProvider,
        Statistics $statistics,
        ArrayUtils $arrayUtils,
        SymfonyStyle  $symfonyStyle
    ) {
        $this->packageMonthlyDownloadsProvider = $packageMonthlyDownloadsProvider;
        $this->statistics = $statistics;
        $this->arrayUtils = $arrayUtils;
        $this->symfonyStyle = $symfonyStyle;
    }

    public function createPackagesData(array $packageNames): array
    {
        $packagesData = [];

        foreach ($packageNames as $packageName) {
            $monthlyDownloads = $this->packageMonthlyDownloadsProvider->provideForPackage($packageName);

            if ($this->shouldSkipPackageForOutlier($packageName, $monthlyDownloads)) {
                continue;
            }

            $lastMonthDailyDownloads = $monthlyDownloads[0];
            $lastYearTrend = $this->statistics->resolveTrend($packageName, $monthlyDownloads, 12);

            $packageData = [
                'package_name' => $packageName,
                'package_short_name' => Strings::after($packageName, '/'),
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

    /**
     * @param int[] $monthlyDownloads
     */
    private function shouldSkipPackageForOutlier(string $packageName, array $monthlyDownloads): bool
    {
        if (in_array($packageName, $this->pseudoPackages, true)) {
            $this->symfonyStyle->note(sprintf(
                'Package "%s" is skipped, because it is only pseudo-package, not real PHP code',
                $packageName
            ));
            return true;
        }

        // not enough data, package younger than 12 months → skip it
        if (! isset($monthlyDownloads[11])) {
            $this->symfonyStyle->note(sprintf(
                'Package "%s" is skipped, because there are no downloads data 12 months back. Found data only for %d months',
                $packageName,
                count($monthlyDownloads)
            ));
            return true;
        }

        $lastMonthDailyDownloads = $monthlyDownloads[0];

        // too small package → skip it
        if ($lastMonthDailyDownloads <= self::MIN_DOWNLOADS_LIMIT) {
            $this->symfonyStyle->note(sprintf(
                'Package "%s" is skipped, because is has only %d downloads last month (%d is bottom limit)',
                $packageName,
                $lastMonthDailyDownloads,
                self::MIN_DOWNLOADS_LIMIT
            ));
            return true;
        }

        $lastYearTrend = $this->statistics->resolveTrend($packageName, $monthlyDownloads, 12);
        if ($lastYearTrend === null) {
            $this->symfonyStyle->note(sprintf(
                'Package "%s" is skipped, because there are no data to count the trend',
                $packageName
            ));
            return true;
        }

        // too fresh package → skip it
        if ($lastYearTrend > self::MAX_TREND_LIMIT) {
            $this->symfonyStyle->note(sprintf(
                'Package "%s" is skipped, because trend %d is too extreme (%d allowed)',
                $packageName,
                $lastYearTrend,
                self::MAX_TREND_LIMIT
            ));
            return true;
        }

        // fresh package jump, probably new interdependency? → skip it
        if ($lastYearTrend > 100) {
            $yearBackMonthDailyDownloads = $monthlyDownloads[11];
            if (($lastMonthDailyDownloads / $yearBackMonthDailyDownloads) > 5) {
                return true;
            }
        }

        return false;
    }
}
