<?php declare(strict_types=1);

namespace TomasVotruba\Website\Result;

use Nette\Utils\DateTime;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Website\ArrayUtils;
use TomasVotruba\Website\Packagist\VendorPackagesProvider;

final class VendorDataFactory
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var VendorPackagesProvider
     */
    private $vendorPackagesProvider;

    /**
     * @var ArrayUtils
     */
    private $arrayUtils;

    /**
     * @var PackageDataFactory
     */
    private $packageDataFactory;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        VendorPackagesProvider $vendorPackagesProvider,
        ArrayUtils $arrayUtils,
        PackageDataFactory $packageDataFactory
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->vendorPackagesProvider = $vendorPackagesProvider;
        $this->arrayUtils = $arrayUtils;
        $this->packageDataFactory = $packageDataFactory;
    }

    /**
     * @param string[] $frameworksVendorToName
     */
    public function createVendorData(array $frameworksVendorToName): array
    {
        $vendorData = [];

        foreach ($frameworksVendorToName as $vendorName => $frameworkName) {
            $this->symfonyStyle->title(sprintf('Loading data for "%s" vendor', $vendorName));

            $vendorPackageNames = $this->vendorPackagesProvider->provideForVendor($vendorName);
            $packagesData = $this->packageDataFactory->createPackagesData($vendorPackageNames);

            $vendorTotalLastYear = $this->arrayUtils->getArrayKeySum($packagesData, 'last_year_total');
            $vendorTotalPreviousYear = $this->arrayUtils->getArrayKeySum($packagesData, 'previous_year_total');
            $averageLastYearTrend = $this->arrayUtils->getArrayKeyAverage($packagesData, 'last_year_trend');

            $vendorData[$vendorName] = [
                'name' => $frameworkName,
                // totals
                'vendor_total_last_year' => $vendorTotalLastYear,
                'vendor_total_previous_year' => $vendorTotalPreviousYear,
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
}
