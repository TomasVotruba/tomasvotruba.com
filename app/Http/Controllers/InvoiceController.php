<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\InputName;
use App\Invoicing\FuelInvoiceExtractor;
use App\ValueObject\FuelInvoice;
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
        private readonly FuelInvoiceExtractor $invoiceSummaryExtractor,
    ) {
    }

    public function __invoke(Request $request): View
    {
        $fuelInvoice = $this->processRequestToFuelInvoice($request);

        return view('helinvoice/invoice', [
            'title' => 'Invoice Converter',
            'fuel_invoice' => $fuelInvoice,
        ]);
    }

    private function processRequestToFuelInvoice(Request $request): ?FuelInvoice
    {
        if (! $request->isMethod(Request::METHOD_POST)) {
            return null;
        }

        $fullTemporaryFilePath = $this->storeFileAndProvideFilePath($request, InputName::INVOICE_PDF);

        $document = $this->pdfParser->parseFile($fullTemporaryFilePath);
        return $this->invoiceSummaryExtractor->resolve($document);
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
