<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Result;

use Nette\Utils\DateTime;
use Symfony\Component\Console\Style\SymfonyStyle;
use TomasVotruba\FrameworkStats\Packagist\VendorPackagesProvider;
use TomasVotruba\FrameworkStats\Sorter;
use TomasVotruba\FrameworkStats\Summer;
use TomasVotruba\FrameworkStats\ValueObject\VendorData;

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
     * @var PackageDataFactory
     */
    private $packageDataFactory;

    /**
     * @var Summer
     */
    private $summer;

    /**
     * @var Sorter
     */
    private $sorter;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        VendorPackagesProvider $vendorPackagesProvider,
        PackageDataFactory $packageDataFactory,
        Summer $summer,
        Sorter $sorter
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->vendorPackagesProvider = $vendorPackagesProvider;
        $this->packageDataFactory = $packageDataFactory;
        $this->summer = $summer;
        $this->sorter = $sorter;
    }

    /**
     * @param string[] $frameworksVendorToName
     */
    public function createVendorsData(array $frameworksVendorToName): array
    {
        $vendorsData = [];

        foreach ($frameworksVendorToName as $vendorName => $frameworkName) {
            $this->symfonyStyle->title(sprintf('Loading data for "%s" vendor', $vendorName));

            $vendorPackageNames = $this->vendorPackagesProvider->provideForVendor($vendorName);
            $packagesData = $this->packageDataFactory->createPackagesData($vendorPackageNames);

            $vendorTotalLastYear = $this->summer->getLastYearTotalArraySum($packagesData);
            $vendorTotalPreviousYear = $this->summer->getPreviousYearTotalArraySum($packagesData);

            $lastYearTrend = ($vendorTotalLastYear / $vendorTotalPreviousYear * 100) - 100;
            $lastYearTrend = round($lastYearTrend, 0);

            $vendorData = new VendorData(
                $frameworkName,
                $vendorTotalLastYear,
                $vendorTotalPreviousYear,
                $lastYearTrend,
                $packagesData
            );

            $vendorsData[$vendorName] = $vendorData;

            $this->symfonyStyle->newLine(2);
        }

        $vendorsData = $this->sorter->sortArrayByLastYearTrend($vendorsData);

        // metadata
        $data['vendors'] = $vendorsData;
        $data['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');

        return $data;
    }
}
