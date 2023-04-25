<?php

declare(strict_types=1);

namespace App\ValueObject;

use Illuminate\Support\Collection;
use Nette\Utils\DateTime;
use Webmozart\Assert\Assert;

/**
 * @api used in blade templates @todo fix in tomasvotruba/unused-public
 */
final class CarReport
{
    /**
     * @param Collection<int, FuelPurchase> $fuelPurchases
     */
    public function __construct(
        private readonly string $plateId,
        private readonly Collection $fuelPurchases,
    ) {
        Assert::false($fuelPurchases->isEmpty());
    }

    public function getPlateId(): string
    {
        return $this->plateId;
    }

    public function getTotalVolume(): float
    {
        return $this->fuelPurchases->sum(static fn (FuelPurchase $fuelPurchase): float => $fuelPurchase->getVolume());
    }

    /**
     * @api used in blade
     */
    public function hasDiscounts(): bool
    {
        return $this->getTotalPrice() !== $this->getTotalPriceAfterDiscount();
    }

    /**
     * @api used in blade
     */
    public function getTotalPrice(): float
    {
        return $this->fuelPurchases->sum(static fn (FuelPurchase $fuelPurchase): float => $fuelPurchase->getPrice());
    }

    /**
     * @api used in blade
     */
    public function getTotalPriceAfterDiscount(): float
    {
        return $this->fuelPurchases->sum(
            static fn (FuelPurchase $fuelPurchase): float => $fuelPurchase->getPriceAfterDiscount()
        );
    }

    public function getFirstFuelPurchaseDate(): DateTime
    {
        return $this->fuelPurchases->first()
            ->getDate();
    }

    public function getLastFuelPurchaseDate(): DateTime
    {
        return $this->fuelPurchases->last()
            ->getDate();
    }

    public function getDateRange(): string
    {
        if (count($this->fuelPurchases) === 1) {
            return $this->getFirstFuelPurchaseDate()
                ->format('Y-m-d');
        }

        return $this->getFirstFuelPurchaseDate()
            ->format('Y-m-d')
            . '—' . $this->getLastFuelPurchaseDate()->format('d');
    }
}
