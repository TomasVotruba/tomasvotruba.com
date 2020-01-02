<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Result;

use Nette\Utils\DateTime;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\Website\ArrayUtils;
use TomasVotruba\Website\Packagist\VendorPackagesProvider;
use TomasVotruba\Website\ValueObject\VendorData;

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
        $vendorDatas = [];

        foreach ($frameworksVendorToName as $vendorName => $frameworkName) {
            $this->symfonyStyle->title(sprintf('Loading data for "%s" vendor', $vendorName));

            $vendorPackageNames = $this->vendorPackagesProvider->provideForVendor($vendorName);
            $packagesData = $this->packageDataFactory->createPackagesData($vendorPackageNames);

            $vendorTotalLastYear = $this->arrayUtils->getArrayKeySum($packagesData, 'last_year_total');
            $vendorTotalPreviousYear = $this->arrayUtils->getArrayKeySum($packagesData, 'previous_year_total');

            $lastYearTrend = ($vendorTotalLastYear / $vendorTotalPreviousYear * 100) - 100;
            $lastYearTrend = round($lastYearTrend, 0);

            $vendorData = new VendorData(
                $frameworkName,
                $vendorTotalLastYear,
                $vendorTotalPreviousYear,
                $lastYearTrend,
                $packagesData
            );

            $vendorDatas[$vendorName] = $vendorData;

            $this->symfonyStyle->newLine(2);
        }

        $vendorDatas = $this->arrayUtils->sortArrayByLastYearTrend($vendorDatas);

        // metadata
        $data['vendors'] = $vendorDatas;
        $data['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');

        return $data;
    }
}
