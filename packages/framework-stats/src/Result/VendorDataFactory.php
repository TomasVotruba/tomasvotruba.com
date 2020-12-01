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
    public function __construct(
        private SymfonyStyle $symfonyStyle,
        private VendorPackagesProvider $vendorPackagesProvider,
        private PackageDataFactory $packageDataFactory,
        private Summer $summer,
        private Sorter $sorter
    ) {
    }

    /**
     * @param string[] $frameworksVendorToName
     */
    public function createVendorsData(array $frameworksVendorToName): array
    {
        $vendorsData = [];

        foreach ($frameworksVendorToName as $vendorName => $frameworkName) {
            $title = sprintf('Loading data for "%s" vendor', $vendorName);
            $this->symfonyStyle->title($title);

            $vendorsData[$vendorName] = $this->createVendorData($vendorName, $frameworkName);

            $this->symfonyStyle->newLine(2);
        }

        $vendorsData = $this->sorter->sortArrayByLastYearTrend($vendorsData);

        // metadata
        $data['vendors'] = $vendorsData;
        $nowDateTime = new DateTime();
        $data['updated_at'] = $nowDateTime->format('Y-m-d H:i:s');

        return $data;
    }

    private function createVendorData(string $vendorName, string $frameworkName): VendorData
    {
        $vendorPackageNames = $this->vendorPackagesProvider->provideForVendor($vendorName);
        $packagesData = $this->packageDataFactory->createPackagesData($vendorPackageNames);

        $vendorTotalLastYear = $this->summer->getLastYearTotalArraySum($packagesData);
        $vendorTotalPreviousYear = $this->summer->getPreviousYearTotalArraySum($packagesData);

        $lastYearTrend = ($vendorTotalLastYear / $vendorTotalPreviousYear * 100) - 100;
        $lastYearTrend = round($lastYearTrend, 0);

        return new VendorData(
            $frameworkName,
            $vendorTotalLastYear,
            $vendorTotalPreviousYear,
            $lastYearTrend,
            $packagesData
        );
    }
}
