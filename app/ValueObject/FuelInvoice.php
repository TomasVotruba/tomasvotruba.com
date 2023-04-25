<?php

declare(strict_types=1);

namespace App\ValueObject;

use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

final class FuelInvoice
{
    /**
     * @param Collection<int, CarReport> $carReports
     */
    public function __construct(
        private readonly float $totalPriceAfterDiscount,
        private readonly Collection $carReports
    ) {
        // meaningful check :)
        Assert::greaterThan($totalPriceAfterDiscount, 100);
    }

    /**
     * @api used in blade
     */
    public function getTotalPriceAfterDiscount(): float
    {
        return $this->totalPriceAfterDiscount;
    }

    /**
     * @api used in blade
     */
    public function areTotalPricesMatching(): bool
    {
        return $this->totalPriceAfterDiscount === $this->getCarReportsTotalPriceAfterDiscount();
    }

    /**
     * @api used in blade
     * @return Collection<int, CarReport>
     */
    public function getCarReports(): Collection
    {
        return $this->carReports;
    }

    /**
     * @api used in blade
     */
    public function getCarReportsTotalPriceAfterDiscount(): float
    {
        return $this->carReports->sum(
            static fn (CarReport $carReport): float => $carReport->getTotalPriceAfterDiscount()
        );
    }
}
