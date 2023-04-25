<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\CarReportExtractor;
use App\Enum\InputName;
use App\ValueObject\CarReport;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;
use Smalot\PdfParser\Parser;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Webmozart\Assert\Assert;

final class InvoiceController extends Controller
{
    public function __construct(
        private readonly Parser $pdfParser,
        private readonly CarReportExtractor $carReportExtractor,
    ) {
    }

    public function __invoke(Request $request): View
    {
        $carReports = $this->processRequestToCarReports($request);
        $carReportsCollection = collect($carReports);

        $invoiceTotalPrice = $carReportsCollection->sum(
            static fn (CarReport $carReport): float => $carReport->getTotalPrice()
        );

        $invoiceTotalPriceAfterDiscount = $carReportsCollection->sum(
            static fn (CarReport $carReport): float => $carReport->getTotalPriceAfterDiscount()
        );

        return view('helinvoice/invoice', [
            'title' => 'Invoice Converter',
            'car_reports' => $carReports,
            'invoice_total_price' => $invoiceTotalPrice,
            'invoice_total_price_after_discount' => $invoiceTotalPriceAfterDiscount,
        ]);
    }

    /**
     * @return CarReport[]
     */
    private function processRequestToCarReports(Request $request): array
    {
        if (! $request->isMethod(Request::METHOD_POST)) {
            return [];
        }

        $fullTemporaryFilePath = $this->storeFileAndProvideFilePath($request, InputName::INVOICE_PDF);

        $document = $this->pdfParser->parseFile($fullTemporaryFilePath);
        $carReports = $this->carReportExtractor->resolve($document);

        // sort from newest date time to the oldest, logically :)
        usort($carReports, static function (CarReport $firstCarReport, CarReport $secondCarReport): int {
            if ($firstCarReport->getFirstFuelPurchaseDate() === $secondCarReport->getFirstFuelPurchaseDate()) {
                return $firstCarReport->getLastFuelPurchaseDate() <=> $secondCarReport->getLastFuelPurchaseDate();
            }

            return $firstCarReport->getFirstFuelPurchaseDate() <=> $secondCarReport->getFirstFuelPurchaseDate();
        });

        return $carReports;
    }

    /**
     * @param InputName::*  $inputName
     */
    private function storeFileAndProvideFilePath(Request $request, string $inputName): string
    {
        $pdfFile = $request->file($inputName);
        if (! $pdfFile instanceof UploadedFile) {
            throw new FileException();
        }

        $temporaryFileName = $pdfFile->store('invoices');
        Assert::string($temporaryFileName);

        $absoluteTemporaryFileName = storage_path($temporaryFileName);
        Assert::fileExists($absoluteTemporaryFileName);

        return $absoluteTemporaryFileName;
    }
}
