<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Result;

use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\FrameworkStats\Exception\ShouldNotHappenException;
use TomasVotruba\FrameworkStats\Packagist\PackageMonthlyDownloadsProvider;
use TomasVotruba\FrameworkStats\Packagist\Purifier\PHPStanNettePackagesPurifier;
use TomasVotruba\FrameworkStats\Sorter;
use TomasVotruba\FrameworkStats\Statistics;
use TomasVotruba\FrameworkStats\ValueObject\PackageData;

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
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var Sorter
     */
    private $sorter;

    /**
     * @var PHPStanNettePackagesPurifier
     */
    private $phpStanNettePackagesPurifier;

    public function __construct(
        PackageMonthlyDownloadsProvider $packageMonthlyDownloadsProvider,
        PHPStanNettePackagesPurifier $phpStanNettePackagesPurifier,
        Statistics $statistics,
        Sorter $sorter,
        SymfonyStyle $symfonyStyle
    ) {
        $this->packageMonthlyDownloadsProvider = $packageMonthlyDownloadsProvider;
        $this->statistics = $statistics;
        $this->symfonyStyle = $symfonyStyle;
        $this->sorter = $sorter;
        $this->phpStanNettePackagesPurifier = $phpStanNettePackagesPurifier;
    }

    public function createPackagesData(array $packageNames): array
    {
        $packagesData = [];

        foreach ($packageNames as $packageName) {
            $monthlyDownloads = $this->packageMonthlyDownloadsProvider->provideForPackage($packageName);

            if ($this->shouldSkipPackageForOutlier($packageName, $monthlyDownloads)) {
                continue;
            }

            // total downloads for 1st year
            $last12Months = $this->getChunkAndExpandDailyAverageToMonthAndSum($monthlyDownloads, 12, 0);
            // total downloads for 2nd years
            $previous12Months = $this->getChunkAndExpandDailyAverageToMonthAndSum($monthlyDownloads, 12, 1);

            if ($previous12Months === 0) {
                // to prevent fatal errors
                continue;
            }

            $last12Months = $this->phpStanNettePackagesPurifier->correctLastYearDownloads(
                $last12Months,
                $packageName
            );

            $previous12Months = $this->phpStanNettePackagesPurifier->correctPreviousYearDownloads(
                $previous12Months,
                $packageName
            );

            $lastYearTrend = 100 * ($last12Months / $previous12Months) - 100;
            if ($lastYearTrend > 300) {
                // too huge trend
                continue;
            }

            $lastYearTrend = round($lastYearTrend, 1);

            $packageData = new PackageData(
                $packageName,
                // numbers
                $lastYearTrend,
                $last12Months,
                $previous12Months
            );

            $packagesData[$packageData->getPackageKey()] = $packageData;
        }

        return $this->sorter->sortArrayByLastYearTrend($packagesData);
    }

    /**
     * @param int[] $monthlyDownloads
     */
    private function shouldSkipPackageForOutlier(string $packageName, array $monthlyDownloads): bool
    {
        // not enough data, package younger than 24 months → skip it
        if (count($monthlyDownloads) < self::MINIMAL_MONTH_AGE - 1) {
            $this->symfonyStyle->note(sprintf(
                'Skipping "%s" package for not enough data. %d months provided, %d needed',
                $packageName,
                count($monthlyDownloads),
                self::MINIMAL_MONTH_AGE
            ));

            return true;
        }

        $firstKey = array_key_first($monthlyDownloads);
        $lastMonthDailyDownloads = $monthlyDownloads[$firstKey];

        if ($lastMonthDailyDownloads < 0) {
            throw new ShouldNotHappenException(sprintf(
                'Last month daily downloads for "%s" package and "%s" month is in minus: %d',
                $packageName,
                $firstKey,
                $lastMonthDailyDownloads
            ));
        }

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

    /**
     * @param int[] $dataByYearMonth
     */
    private function getChunkAndExpandDailyAverageToMonthAndSum(
        array $dataByYearMonth,
        int $chunkSize,
        int $chunkPosition
    ): int {
        $chunks = array_chunk($dataByYearMonth, $chunkSize, true);

        $chunk = $chunks[$chunkPosition];

        $expandedChunk = $this->statistics->expandDailyAverageToMonthTotal($chunk);

        return (int) array_sum($expandedChunk);
    }
}
