<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\ValueObject;

use TomasVotruba\FrameworkStats\Contract\LastYearTrendAwareInterface;

final class VendorData implements LastYearTrendAwareInterface
{
    /**
     * @var string
     */
    private $vendorName;

    /**
     * @var int
     */
    private $vendorTotalLastYear;

    /**
     * @var int
     */
    private $vendorTotalPreviousYear;

    /**
     * @var float
     */
    private $lastYearTrend;

    /**
     * @var PackageData[]
     */
    private $packagesData = [];

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
