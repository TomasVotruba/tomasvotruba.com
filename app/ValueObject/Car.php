<?php

declare(strict_types=1);

namespace App\ValueObject;

/**
 * @api used in blade
 */
final readonly class Car
{
    public function __construct(
        private string $plate,
        private string $driverName,
        private string $carName,
    ) {
    }

    public function getPlate(): string
    {
        return str_replace(' ', '', $this->plate);
    }

    public function getDriverName(): string
    {
        return $this->driverName;
    }

    public function getCarName(): string
    {
        return $this->carName;
    }
}
