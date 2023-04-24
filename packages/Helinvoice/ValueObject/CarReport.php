<?php

declare(strict_types=1);

namespace App\Helinvoice\ValueObject;

use Webmozart\Assert\Assert;

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
        foreach ($this->fuelPurchases   as $fuelPurchase) {
            $amount += $fuelPurchase->getVolume();
        }

        return $amount;
    }

    public function getTotalPrice(): float
    {
        $amount = 0.0;
        foreach ($this->fuelPurchases   as $fuelPurchase) {
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

    public function getDateRange(): string
    {
        if (count($this->fuelPurchases) === 1) {
            return $this->fuelPurchases[0]->getDate()->format('Y-m-d');
        }

        $firstKey = array_key_first($this->fuelPurchases);
        $lastKey = array_key_last($this->fuelPurchases);

        $firstFuelPurchase = $this->fuelPurchases[$firstKey];
        $lastFuelPurchase = $this->fuelPurchases[$lastKey];

        return $firstFuelPurchase->getDate()
            ->format('Y-m-d') . '—' . $lastFuelPurchase->getDate()->format('d');
    }
}
