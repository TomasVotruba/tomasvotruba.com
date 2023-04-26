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
     * @var float
     */
    private const PRICE_WITH_TAX_RATE = 1.22;

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

    public function getReadablePlateId(): string
    {
        return substr($this->plateId, 0, 2) . ' ' .
            substr($this->plateId, 2, 3) . ' ' .
            substr($this->plateId, 5, 2);
    }

    public function getTotalVolume(): float
    {
        return $this->fuelPurchases->sum(static fn (FuelPurchase $fuelPurchase): float => $fuelPurchase->getVolume());
    }

    /**
     * @api used in blade
     */
    public function getTotalPrice(): float
    {
        return $this->fuelPurchases->sum(
            static fn (FuelPurchase $fuelPurchase): float => $fuelPurchase->getPrice()
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

    public function getTax(): float
    {
        return $this->getTotalPrice() - $this->getBasePrice();
    }

    public function getBasePrice(): float
    {
        return $this->getTotalPrice() / self::PRICE_WITH_TAX_RATE;
    }

    public function getFB(): float
    {
        return $this->getBasePrice() * .4;
    }

    public function getFD(): float
    {
        return $this->getBasePrice() * .6;
    }
}
