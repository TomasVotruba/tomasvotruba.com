<?php

declare(strict_types=1);

namespace App\Repository;

use App\ValueObject\Car;
use Nette\Utils\FileSystem;
use Nette\Utils\Json;

final class CarRepository
{
    /**
     * @var Car[]
     */
    private array $cars = [];

    public function __construct()
    {
        $carsFileContents = FileSystem::read(__DIR__ . '/../../resources/database/cars.json');
        $carsJson = Json::decode($carsFileContents, Json::FORCE_ARRAY);

        foreach ($carsJson as $carJson) {
            $this->cars[] = new Car($carJson['car_plate'], $carJson['driver_name'], (string) $carJson['car_name']);
        }
    }

    public function getCarByPlate(string $plate): ?Car
    {
        foreach ($this->cars as $car) {
            if ($car->getPlate() === $plate) {
                return $car;
            }
        }

        return null;
    }
}
