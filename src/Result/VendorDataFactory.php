<?php declare(strict_types=1);

namespace TomasVotruba\Website\Result;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Website\ArrayUtils;
use TomasVotruba\Website\Packagist\PackageMonthlyDownloadsProvider;
use TomasVotruba\Website\Packagist\VendorPackagesProvider;
use TomasVotruba\Website\Statistics;

final class VendorDataFactory
{
    /**
     * @var string[]
     */
    private $frameworksVendorToName = [];

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var PackageMonthlyDownloadsProvider
     */
    private $packageMonthlyDownloadsProvider;

    /**
     * @var VendorPackagesProvider
     */
    private $vendorPackagesProvider;

    /**
     * @var ArrayUtils
     */
    private $arrayUtils;

    /**
     * @var Statistics
     */
    private $statistics;

    /**
     * @param string[] $frameworksVendorToName
     */
    public function __construct(
        SymfonyStyle $symfonyStyle,
        PackageMonthlyDownloadsProvider $packageMonthlyDownloadsProvider,
        VendorPackagesProvider $vendorPackagesProvider,
        ArrayUtils $arrayUtils,
        Statistics $statistics,
        array $frameworksVendorToName
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->packageMonthlyDownloadsProvider = $packageMonthlyDownloadsProvider;
        $this->vendorPackagesProvider = $vendorPackagesProvider;
        $this->arrayUtils = $arrayUtils;
        $this->statistics = $statistics;
        $this->frameworksVendorToName = $frameworksVendorToName;
    }

    public function createVendorData(): array
    {
        $vendorData = [];

        foreach ($this->frameworksVendorToName as $vendorName => $frameworkName) {
            $this->symfonyStyle->title(sprintf('Loading data for "%s" vendor', $vendorName));

            $vendorPackageNames = $this->vendorPackagesProvider->provideForVendor($vendorName);
            $packagesData = $this->createPackagesData($vendorPackageNames);

            $vendorTotalLastMonth = $this->arrayUtils->getArrayKeySum(
                $packagesData,
                'last_month_average_daily_downloads'
            );
            $vendorTotalLastYear = $this->arrayUtils->getArrayKeySum($packagesData, 'last_year_total');
            $averageLastYearTrend = $this->arrayUtils->getArrayKeyAverage($packagesData, 'last_year_trend');

            $vendorData[$vendorName] = [
                'name' => $frameworkName,
                // totals
                'vendor_total_last_month' => $vendorTotalLastMonth,
                'vendor_total_last_year' => $vendorTotalLastYear,
                'average_last_year_trend' => $averageLastYearTrend,
                // packages details
                'packages_data' => $packagesData,
            ];
        }

        $vendorData = $this->arrayUtils->sortDataByKey($vendorData, 'average_last_year_trend');

        // metadata
        $data['vendors'] = $vendorData;
        $data['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');

        return $data;
    }

    private function createPackagesData(array $packageNames): array
    {
        $packagesData = [];

        foreach ($packageNames as $packageName) {
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

            $lastYearTrend = $this->statistics->resolveTrend($monthlyDownloads, 12);
            if ($lastYearTrend === null) {
                continue;
            }

            // too fresh package → skip it
            if ($lastYearTrend > 300) {
                continue;
            }

            $packageData = [
                'package_name' => $packageName,
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
