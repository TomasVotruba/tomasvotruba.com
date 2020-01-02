<?php

declare(strict_types=1);

namespace TomasVotruba\Website\ValueObject;

use Nette\Utils\Strings;

final class PackageData
{
    /**
     * @var string
     */
    private $packageName;

    /**
     * @var float
     */
    private $lastYearTrend;

    /**
     * @var int
     */
    private $last12Months;

    /**
     * @var int
     */
    private $previous12Months;

    /**
     * @var string
     */
    private $packageShortName;

    public function __construct(
        string $packageName,
        float $lastYearTrend,
        int $last12Months,
        int $previous12Months
    ) {
        $this->packageName = $packageName;
        $this->packageShortName = (string) Strings::after($packageName, '/');

        $this->lastYearTrend = $lastYearTrend;
        $this->last12Months = $last12Months;
        $this->previous12Months = $previous12Months;
    }

    public function getPackageName(): string
    {
        return $this->packageName;
    }

    public function getLastYearTrend(): float
    {
        return $this->lastYearTrend;
    }

    public function getLast12Months(): int
    {
        return $this->last12Months;
    }

    public function getPrevious12Months(): int
    {
        return $this->previous12Months;
    }

    public function getPackageShortName(): string
    {
        return $this->packageShortName;
    }
}
