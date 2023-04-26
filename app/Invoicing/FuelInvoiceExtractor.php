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
        $carReports = $this->carReportExtractor->resolve($document);

        // @todo resolve

        [$invoiceNumber, $invoiceDate] = $this->resolveInvoiceNumberAndDate($document);

        return new FuelInvoice($invoiceTotalAmount, $invoiceNumber, $invoiceDate, $carReports);
    }

    private function resolveTotalPrice(Document $document): float
    {
        foreach ($document->getPages() as $page) {
            $match = Strings::match($page->getText(), '#TOTALE\s+([\d\.]+,\d+)\s+(?<total_price>[\d\.]+,\d+)#');
            if (! is_array($match)) {
                continue;
            }

            $numericValue = str_replace(['.', ','], ['', '.'], (string) $match['total_price']);
            return (float) $numericValue;
        }

        throw new ShouldNotHappenException('Total invoice amount was not found');
    }

    /**
     * @return array{string, string}
     */
    private function resolveInvoiceNumberAndDate(Document $document): array
    {
        foreach ($document->getPages() as $page) {
            $match = Strings::match($page->getText(), '#FATTURA N. (?<invoice_number>.*?) del (?<invoice_date>.*?)\s#');
            if (! is_array($match)) {
                continue;
            }

            $invoiceNumber = (string) $match['invoice_number'];
            $invoiceDate = (string) $match['invoice_date'];

            return [$invoiceNumber, $invoiceDate];
        }

        throw new ShouldNotHappenException('Invoice number and date was not found');
    }
}
