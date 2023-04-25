<?php

declare(strict_types=1);

namespace App\ValueObject;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Webmozart\Assert\Assert;

final class FuelPurchase
{
    public function __construct(
        private readonly string $date,
        private readonly int $kilometres,
        private readonly float $volume,
        private readonly float $price,
        private readonly float $priceAfterDiscount
    ) {
    }

    public function getDate(): DateTime
    {
        $match = Strings::match($this->date, '#(?<day>\d+)\/(?<month>\d+)\/(?<year>\d+)#');
        Assert::isArray($match);

        $standardDate = '20' . $match['year'] . '-' . $match['month'] . '-' . $match['day'];

        return DateTime::from($standardDate);
    }

    /**
     * @api where this should be used?
     */
    public function getKilometres(): float
    {
        return $this->kilometres;
    }

    public function getVolume(): float
    {
        return $this->volume;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getPriceAfterDiscount(): float
    {
        return $this->priceAfterDiscount;
    }
}
