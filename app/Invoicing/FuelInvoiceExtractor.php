<?php

declare(strict_types=1);

namespace App\Invoicing;

use App\ValueObject\FuelInvoice;
use Nette\Utils\Strings;
use Smalot\PdfParser\Document;
use Symplify\PHPStanExtensions\Exception\ShouldNotHappenException;

final class FuelInvoiceExtractor
{
    public function __construct(
        private readonly CarReportExtractor $carReportExtractor,
    ) {
    }

    public function resolve(Document $document): FuelInvoice
    {
        $invoiceTotalAmount = $this->resolveTotalPrice($document);
        $collection = $this->carReportExtractor->resolve($document);

        return new FuelInvoice($invoiceTotalAmount, $collection);
    }

    private function resolveTotalPrice(Document $document): float
    {
        foreach ($document->getPages() as $page) {
            if (! str_contains($page->getText(), 'TOTALE FATTURA')) {
                continue;
            }

            $match = Strings::match($page->getText(), '#TOTALE\s+([\d\.]+,\d+)\s+(?<total_price>[\d\.]+,\d+)#');
            if (! is_array($match)) {
                continue;
            }

            $numericValue = str_replace(['.', ','], ['', '.'], (string) $match['total_price']);
            return (float) $numericValue;
        }

        throw new ShouldNotHappenException('Total invoice amount was not found');
    }
}
