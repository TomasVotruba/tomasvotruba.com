<?php

declare(strict_types=1);

namespace App\Http\Controllers\Helinvoice;

use App\Helinvoice\CarReportExtractor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;
use Smalot\PdfParser\Parser;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Webmozart\Assert\Assert;

final class ProcessInvoiceFormController extends Controller
{
    public function __construct(
        private readonly Parser $pdfParser,
        private readonly CarReportExtractor $carReportExtractor,
    ) {
    }

    public function __invoke(Request $request): View
    {
        $fullTemporaryFilePath = $this->storeFileAndProvideFilePath($request, 'invoice_pdf');

        $document = $this->pdfParser->parseFile($fullTemporaryFilePath);
        $carReports = $this->carReportExtractor->resolve($document);

        // render car reports :)

        // @todo render table
        // 1) headlines: ['#', 'Car Plate', 'Date Period', 'Volume (l)', 'Price (€)', 'After Discount (€)'],
        // 2) render to blade template - $this->carsTableFactory->createTableRows($carReports);

        dd($carReports);
    }

    private function storeFileAndProvideFilePath(Request $request, string $inputName): string
    {
        $pdfFile = $request->file($inputName);
        if (! $pdfFile instanceof UploadedFile) {
            throw new FileException();
        }

        $temporaryFileName = $pdfFile->store('invoices');
        Assert::string($temporaryFileName);

        return storage_path($temporaryFileName);
    }
}
