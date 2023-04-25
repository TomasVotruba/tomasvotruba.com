<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\InputName;
use App\Helinvoice\CarReportExtractor;
use App\Helinvoice\ValueObject\CarReport;
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
        $carReports = null;

        if ($request->isMethod(Request::METHOD_POST)) {
            $carReports = $this->processRequest($request);
        }

        return view('helinvoice/invoice', [
            'title' => 'Invoice Converter',
            'car_reports' => $carReports,
        ]);
    }

    /**
     * @return CarReport[]
     */
    private function processRequest(Request $request): array
    {
        $fullTemporaryFilePath = $this->storeFileAndProvideFilePath($request, InputName::INVOICE_PDF);

        $document = $this->pdfParser->parseFile($fullTemporaryFilePath);
        return $this->carReportExtractor->resolve($document);
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
