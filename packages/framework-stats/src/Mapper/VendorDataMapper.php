<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Mapper;

use TomasVotruba\FrameworkStats\ValueObject\VendorData;

final class VendorDataMapper
{
    public function __construct(
        private PackageDataMapper $packageDataMapper
    ) {
    }

    public function mapObjectToArray(VendorData $vendorData): array
    {
        $vendorDataArray['vendor_name'] = $vendorData->getVendorName();
        $vendorDataArray['vendor_total_last_year'] = $vendorData->getVendorTotalLastYear();
        $vendorDataArray['vendor_total_previous_year'] = $vendorData->getVendorTotalPreviousYear();
        $vendorDataArray['last_year_trend'] = $vendorData->getLastYearTrend();

        foreach ($vendorData->getPackagesData() as $packageData) {
            $vendorDataArray['packages_data'][] = $this->packageDataMapper->mapObjectToArray($packageData);
        }

        return $vendorDataArray;
    }
}
