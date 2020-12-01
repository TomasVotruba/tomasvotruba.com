<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\ValueObject;

use TomasVotruba\FrameworkStats\Contract\LastYearTrendAwareInterface;

final class VendorData implements LastYearTrendAwareInterface
{
    /**
     * @param PackageData[] $packagesData
     */
    public function __construct(
        private string $vendorName,
        private int $vendorTotalLastYear,
        private int $vendorTotalPreviousYear,
        private float $lastYearTrend,
        private array $packagesData
    ) {
    }

    public function getVendorName(): string
    {
        return $this->vendorName;
    }

    public function getVendorTotalLastYear(): int
    {
        return $this->vendorTotalLastYear;
    }

    public function getVendorTotalPreviousYear(): int
    {
        return $this->vendorTotalPreviousYear;
    }

    public function getLastYearTrend(): float
    {
        return $this->lastYearTrend;
    }

    /**
     * @return PackageData[]
     */
    public function getPackagesData(): array
    {
        return $this->packagesData;
    }
}
