<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Mapper;

use TomasVotruba\FrameworkStats\ValueObject\PackageData;

final class PackageDataMapper
{
    public function mapObjectToArray(PackageData $packageData): array
    {
        $packageDataArray = [];
        $packageDataArray['package_name'] = $packageData->getPackageName();
        $packageDataArray['package_short_name'] = $packageData->getPackageShortName();
        $packageDataArray['last_12_months'] = $packageData->getLast12Months();
        $packageDataArray['previous_12_months'] = $packageData->getPrevious12Months();
        $packageDataArray['last_year_trend'] = $packageData->getLastYearTrend();

        return $packageDataArray;
    }
}
