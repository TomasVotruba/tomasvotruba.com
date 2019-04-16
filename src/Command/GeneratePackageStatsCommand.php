<?php declare(strict_types=1);

namespace TomasVotruba\Website\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\Statie\FileSystem\GeneratedFilesDumper;
use TomasVotruba\Website\Result\VendorDataFactory;

final class GeneratePackageStatsCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var GeneratedFilesDumper
     */
    private $generatedFilesDumper;

    /**
     * @var VendorDataFactory
     */
    private $vendorDataFactory;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        GeneratedFilesDumper $generatedFilesDumper,
        VendorDataFactory $vendorDataFactory
    ) {
        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
        $this->generatedFilesDumper = $generatedFilesDumper;
        $this->vendorDataFactory = $vendorDataFactory;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Generates downloads stats data for PHP frameworks');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $vendorData = $this->vendorDataFactory->createVendorData();
        $this->generatedFilesDumper->dump('php_framework_trends', $vendorData);
        $this->symfonyStyle->success('Data imported!');

        return ShellCode::SUCCESS;
    }
}
