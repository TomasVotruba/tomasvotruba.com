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
    private const MINIMAL_MONTH_AGE = 24;

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
        SymfonyStyle $symfonyStyle
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

            $last12Months = $this->statistics->resolveTotal($monthlyDownloads, 12, 0);
            $previous12Months = $this->statistics->resolveTotal($monthlyDownloads, 12, 11);

            if ($previous12Months === 0) {
                // to prevent fatal errors
                continue;
            }

            $lastYearTrend = 100 * ($last12Months / $previous12Months) - 100; // $this->statistics->resolveTrend($packageName, $monthlyDownloads, 24);
            if ($lastYearTrend > 300) {
                // too huge trend
                continue;
            }

            $lastYearTrend = round($lastYearTrend, 1);

            $packageData = [
                'package_name' => $packageName,
                'package_short_name' => Strings::after($packageName, '/'),
                // numbers
                'last_year_trend' => $lastYearTrend,
                'last_year_total' => $last12Months,
                'previous_year_total' => $previous12Months,
            ];

            $packageKey = $this->createPackageKey($packageName);
            $packagesData[$packageKey] = $packageData;
        }

        return $this->arrayUtils->sortDataByKey($packagesData, 'last_year_trend');
    }

    /**
     * @param int[] $monthlyDownloads
     */
    private function shouldSkipPackageForOutlier(string $packageName, array $monthlyDownloads): bool
    {
        // not enough data, package younger than 24 months → skip it
        if (! isset($monthlyDownloads[self::MINIMAL_MONTH_AGE - 1])) {
            $this->symfonyStyle->note(sprintf(
                'Skipping "%s" package for not enough data. %d months provided, %d needed',
                $packageName,
                count($monthlyDownloads),
                self::MINIMAL_MONTH_AGE
            ));

            return true;
        }

        $lastMonthDailyDownloads = $monthlyDownloads[0];

        // too small package → skip it
        if ($lastMonthDailyDownloads <= self::MIN_DOWNLOADS_LIMIT) {
            $this->symfonyStyle->note(sprintf(
                'Skipping "%s" package for not enough downloads last month. %d provided, %d needed',
                $packageName,
                $lastMonthDailyDownloads,
                self::MIN_DOWNLOADS_LIMIT
            ));

            return true;
        }

        return false;
    }

    private function createPackageKey(string $packageName): string
    {
        return Strings::replace($packageName, '#(/|-)#', '_');
    }
}
