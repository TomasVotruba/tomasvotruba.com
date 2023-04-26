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
        private readonly string $invoiceNumber,
        private readonly string $invoiceDate,
        private readonly float $totalBase,
        private readonly float $totalTax,
        private readonly float $totalPrice,
        private readonly Collection $carReports,
    ) {
        // meaningful check :)
        Assert::greaterThan($totalPrice, 100);
    }

    /**
     * @api used in blade
     */
    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    /**
     * @api used in blade
     */
    public function areTotalPricesMatching(): bool
    {
        return $this->totalPrice === $this->getCarReportsTotalPrice();
    }

    /**
     * @api used in blade
     */
    public function areBasePriceMatching(): bool
    {
        return $this->totalBase === $this->getCarReportsBasePriceTotal();
    }

    /**
     * @api used in blade
     */
    public function areTaxPriceMatching(): bool
    {
        return $this->totalTax === $this->getCarReportsTaxTotal();
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
    public function getCarReportsBasePriceTotal(): float
    {
        $carReportsBasePriceTotal = $this->carReports->sum(
            static fn (CarReport $carReport): float => $carReport->getBasePrice()
        );

        return round($carReportsBasePriceTotal, 2);
    }

    /**
     * @api used in blade
     */
    public function getCarReportsTaxTotal(): float
    {
        $carReportsTotalTax = $this->carReports->sum(
            static fn (CarReport $carReport): float => $carReport->getTax()
        );

        return round($carReportsTotalTax, 2);
    }

    /**
     * @api used in blade
     */
    public function getCarReportsTotalPrice(): float
    {
        $carReportsTotalPrice = $this->carReports->sum(
            static fn (CarReport $carReport): float => $carReport->getTotalPrice()
        );

        return round($carReportsTotalPrice, 2);
    }

    /**
     * @api used in blade
     */
    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    /**
     * @api used in blade
     */
    public function getInvoiceDate(): string
    {
        return $this->invoiceDate;
    }

    /**
     * @api used in blade
     */
    public function getTotalBase(): float
    {
        return $this->totalBase;
    }

    /**
     * @api used in blade
     */
    public function getTotalTax(): float
    {
        return $this->totalTax;
    }
}
