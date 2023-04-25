<?php

declare(strict_types=1);

namespace App\ValueObject;

use Nette\Utils\DateTime;
use Webmozart\Assert\Assert;

/**
 * @api used in blade templates @todo fix in tomasvotruba/unused-public
 */
final class CarReport
{
    /**
     * @param FuelPurchase[] $fuelPurchases
     */
    public function __construct(
        private readonly string $plateId,
        private readonly array $fuelPurchases,
    ) {
        // at least 1 record is required
        Assert::notEmpty($fuelPurchases);
        Assert::allIsInstanceOf($fuelPurchases, FuelPurchase::class);
    }

    public function getPlateId(): string
    {
        return $this->plateId;
    }

    public function getTotalVolume(): float
    {
        $amount = 0.0;
        foreach ($this->fuelPurchases as $fuelPurchase) {
            $amount += $fuelPurchase->getVolume();
        }

        return $amount;
    }

    public function hasDiscounts(): bool
    {
        return $this->getTotalPrice() !== $this->getTotalPriceAfterDiscount();
    }

    public function getTotalPrice(): float
    {
        $amount = 0.0;
        foreach ($this->fuelPurchases as $fuelPurchase) {
            $amount += $fuelPurchase->getPrice();
        }

        return $amount;
    }

    public function getTotalPriceAfterDiscount(): float
    {
        $amount = 0.0;
        foreach ($this->fuelPurchases   as $fuelPurchase) {
            $amount += $fuelPurchase->getPriceAfterDiscount();
        }

        return $amount;
    }

    public function getFirstFuelPurchaseDate(): DateTime
    {
        return $this->getFirstFuelPurchase()
            ->getDate();
    }

    public function getLastFuelPurchaseDate(): DateTime
    {
        return $this->getLastFuelPurchase()
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

    private function getLastFuelPurchase(): FuelPurchase
    {
        $lastKey = array_key_last($this->fuelPurchases);
        return $this->fuelPurchases[$lastKey];
    }

    private function getFirstFuelPurchase(): FuelPurchase
    {
        $firstKey = array_key_first($this->fuelPurchases);
        return $this->fuelPurchases[$firstKey];
    }
}
