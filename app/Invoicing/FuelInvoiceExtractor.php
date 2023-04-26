<?php

declare(strict_types=1);

namespace App\Invoicing;

use App\Utils\Numberic;
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
        [$totalBase, $totalTax] = $this->resolveTotalBaseAndTotalTax($document);

        [$invoiceNumber, $invoiceDate] = $this->resolveInvoiceNumberAndDate($document);

        return new FuelInvoice($invoiceNumber, $invoiceDate, $totalBase, $totalTax, $invoiceTotalAmount, $carReports);
    }

    private function resolveTotalPrice(Document $document): float
    {
        foreach ($document->getPages() as $page) {
            $match = Strings::match($page->getText(), '#TOTALE\s+([\d\.]+,\d+)\s+(?<total_price>[\d\.]+,\d+)#');
            if (! is_array($match)) {
                continue;
            }

            return Numberic::stringToFloat($match['total_price']);
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

    /**
     * @return array{float, float}
     */
    private function resolveTotalBaseAndTotalTax(Document $document): array
    {
        foreach ($document->getPages() as $page) {
            if (! str_contains($page->getText(), 'RIEPILOGO IVA')) {
                continue;
            }

            $match = Strings::match($page->getText(), '#IMPONIBILE\s+(?<base_total>.*?)\s+(?<tax_total>.*?)\s+#');
            if (! is_array($match)) {
                continue;
            }

            $baseTotal = Numberic::stringToFloat($match['base_total']);
            $taxTotal = Numberic::stringToFloat($match['tax_total']);

            return [$baseTotal, $taxTotal];
        }

        throw new ShouldNotHappenException('Invoice base and tax was not found');
    }
}
