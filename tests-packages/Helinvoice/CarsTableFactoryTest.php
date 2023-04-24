<?php

declare(strict_types=1);

namespace App\Tests\Helinvoice;

use App\Helinvoice\CarsTableFactory;
use App\Helinvoice\ValueObject\CarReport;
use App\Helinvoice\ValueObject\FuelPurchase;
use App\Tests\AbstractTestCase;

final class CarsTableFactoryTest extends AbstractTestCase
{
    public function test(): void
    {
        $carsTableFactory = $this->make(CarsTableFactory::class);

        $carReport = new CarReport('ABC12', [new FuelPurchase('05/05/1989', 100, 25.00, 30.00, 15.00)]);

        $tableRows = $carsTableFactory->createTableRows([$carReport]);

        $expectedRow = [1, 'ABC12', '1989-05-05', 25.0, '30.00', '15.00'];
        $this->assertSame([$expectedRow], $tableRows);
    }
}
