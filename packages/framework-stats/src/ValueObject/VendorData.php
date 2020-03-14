<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\ValueObject;

use TomasVotruba\FrameworkStats\Contract\LastYearTrendAwareInterface;

final class VendorData implements LastYearTrendAwareInterface
{
    private string $vendorName;

    private int $vendorTotalLastYear;

    private int $vendorTotalPreviousYear;

    private float $lastYearTrend;

    /**
     * @var PackageData[]
     */
    private array $packagesData = [];

    /**
     * @param PackageData[] $packagesData
     */
    public function __construct(
        string $vendorName,
        int $vendorTotalLastYear,
        int $vendorTotalPreviousYear,
        float $lastYearTrend,
        array $packagesData
    ) {
        $this->vendorName = $vendorName;
        $this->vendorTotalLastYear = $vendorTotalLastYear;
        $this->vendorTotalPreviousYear = $vendorTotalPreviousYear;
        $this->lastYearTrend = $lastYearTrend;
        $this->packagesData = $packagesData;
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
