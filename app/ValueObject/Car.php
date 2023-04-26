<?php

declare(strict_types=1);

namespace App\ValueObject;

/**
 * @api used in blade
 */
final class Car
{
    public function __construct(
        private readonly string $plate,
        private readonly string $driverName,
        private readonly string $carName,
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
