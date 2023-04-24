<?php

declare(strict_types=1);

namespace App\Helinvoice;

use App\Helinvoice\ValueObject\CarReport;
use App\Helinvoice\ValueObject\FuelPurchase;
use Nette\Utils\Strings;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Page;
use Webmozart\Assert\Assert;

final class CarReportExtractor
{
    /**
     * Matching typical line content:
     * "7028009678100061017 00033 06/09/22 1739  SSP  0123 007500 1135 VIA TRIESTE 6 DISTR. VIMERCATE      PP    49,99      29,60 1,689 0,000 1,689    49,99"
     *
     * @see https://regex101.com/r/zep7mO/1
     * @var string
     */
    private const FUEL_PURCHASE_REGEX = '#
        (?<num_card>\d{19})\s+
        (?<num_ticket>\d+)\s+
        (?<date>\d+\/\d+/\d+)\s
        (?<time>\d+)\s+
        (?<product_code>\w+)\s+
        (?<code_vehicle>\d+)\s+
        (?<kilometres>\d+)\s+
        (.*?)
        (?<sr>SV|PP|SF|SV)\s+
        (?<price>\d+\,\d+)\s+
        (?<volume>\d+\,\d+)\s+
        (?<basic_price>\d+,\d+)\s+
        (?<premium_discount>\d+,\d+)\s+
        (?<final_price>\d+,\d+)\s+
        (?<price_total>\d+,\d+)
    #x';

    /**
     * @return CarReport[]
     */
    public function resolve(Document $document): array
    {
        $carReports = [];

        foreach ($document->getPages() as $page) {
            if (! $this->isInvoiceTable($page)) {
                continue;
            }

            $fuelPurchases = [];

            foreach ($page->getDataTm() as $lineData) {
                // key "0": position metadata
                // key "1": string contents
                $lineContents = $lineData[1];

                // car cost item
                if (! str_contains((string) $lineContents, 'TOTALE PAN')) {
                    $fuelPurchase = $this->createFullPurchaseIfMatch($lineContents);
                    if ($fuelPurchase instanceof FuelPurchase) {
                        $fuelPurchases[] = $fuelPurchase;
                    }
                } else {
                    // end of current car → sumup and add to itmes
                    $match = Strings::match($lineContents, '#TARGA/NOME\s+(?<plate_id>\w+)#');
                    Assert::isArray($match);

                    $plateId = $match['plate_id'];
                    Assert::string($plateId);

                    $carReports[] = new CarReport($plateId, $fuelPurchases);

                    // reset for the next car
                    $fuelPurchases = [];
                }
            }
        }

        Assert::allIsInstanceOf($carReports, CarReport::class);

        return $carReports;
    }

    private function createFullPurchaseIfMatch(string $lineContents): ?FuelPurchase
    {
        $match = Strings::match($lineContents, self::FUEL_PURCHASE_REGEX);
        if ($match === null) {
            return null;
        }

        return new FuelPurchase(
            $match['date'],
            (int) $match['kilometres'],
            $this->convertStringToFloat($match['volume']),
            $this->convertStringToFloat($match['price']),
            $this->convertStringToFloat($match['price_total']),
        );
    }

    private function convertStringToFloat(string $amount): float
    {
        return (float) str_replace(',', '.', $amount);
    }

    private function isInvoiceTable(Page $page): bool
    {
        return str_contains($page->getText(), 'ALLEGATO ALLA FATTURA');
    }
}
