<?php

declare(strict_types=1);

namespace App\Commands;

use App\Helinvoice\CarReportExtractor;
use App\Helinvoice\CarsTableFactory;
use Illuminate\Console\Command;
use Smalot\PdfParser\Parser;
use Symfony\Component\Console\Helper\TableStyle;

final class ProcessInvoiceCommand extends Command
{
    /**
     * @var string
     */
    private const INPUT_INVOICE_FILE_PATH = __DIR__ . '/../../../resources/invoices/kuwait.pdf';

    /**
     * @var string
     */
    protected $signature = 'run';

    /**
     * @var string
     */
    protected $description = 'Generate Excel sheet with data based on PDF Invoice';

    public function __construct(
        /**
         * @see https://github.com/smalot/pdfparser
         */
        private readonly Parser $pdfParser,
        private readonly CarsTableFactory $carsTableFactory,
        private readonly CarReportExtractor $carReportExtractor,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $document = $this->pdfParser->parseFile(self::INPUT_INVOICE_FILE_PATH);

        $this->line(
            sprintf(
                '<info>Parsing PDF invoice</info>: <comment>%s</comment>',
                pathinfo(self::INPUT_INVOICE_FILE_PATH, PATHINFO_BASENAME)
            )
        );
        $this->newLine();

        $carReports = $this->carReportExtractor->resolve($document);

        $this->comment(sprintf('Found %d car purchases', count($carReports)));
        $this->newLine();

        $tableRows = $this->carsTableFactory->createTableRows($carReports);

        $columnStyles = [
            (new TableStyle())->setPadType(STR_PAD_LEFT),
            (new TableStyle())->setPadType(STR_PAD_BOTH),
            (new TableStyle())->setPadType(STR_PAD_BOTH),
            (new TableStyle())->setPadType(STR_PAD_LEFT),
            (new TableStyle())->setPadType(STR_PAD_LEFT),
            (new TableStyle())->setPadType(STR_PAD_LEFT),
        ];

        $this->table(
            ['#', 'Car Plate', 'Date Period', 'Volume (l)', 'Price (€)', 'After Discount (€)'],
            $tableRows,
            'default',
            $columnStyles
        );
        $this->newLine();

        // @todo check with Heli what is important and what not + generate Sheet
        // @todo add invoice sum cross check!

        return self::SUCCESS;
    }
}
