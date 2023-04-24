<?php

declare(strict_types=1);

namespace App\Helinvoice;

use App\Helinvoice\ValueObject\CarReport;
use Webmozart\Assert\Assert;

final class CarsTableFactory
{
    /**
     * @api
     * @param CarReport[] $carReports
     * @return array<mixed[]>
     */
    public function createTableRows(array $carReports): array
    {
        Assert::allIsInstanceOf($carReports, CarReport::class);

        // render car purchases with total values in a beautiful table :)
        $tableRows = [];

        foreach ($carReports as $key => $carReport) {
            $totalPrice = $carReport->getTotalPrice();
            $totalPriceAfterDiscount = $carReport->getTotalPriceAfterDiscount();

            $tableRows[] = [
                $key + 1,
                $carReport->getPlateId(),
                $carReport->getDateRange(),
                $carReport->getTotalVolume(),
                number_format($totalPrice, 2),
                // show only in case of change, to make copy-pasting easier
                ($totalPrice !== $totalPriceAfterDiscount) ? number_format($totalPriceAfterDiscount, 2) : '-',
            ];
        }

        return $tableRows;
    }
}
