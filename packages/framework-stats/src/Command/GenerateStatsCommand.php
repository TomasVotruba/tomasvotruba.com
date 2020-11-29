<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\FrameworkStats\Mapper\VendorDataMapper;
use TomasVotruba\FrameworkStats\Result\VendorDataFactory;
use TomasVotruba\Website\FileSystem\ParametersConfigDumper;
use TomasVotruba\Website\ValueObject\Option;

final class GenerateStatsCommand extends Command
{
    /**
     * @var string
     */
    private const VENDORS = 'vendors';

    private SymfonyStyle $symfonyStyle;

    private VendorDataFactory $vendorDataFactory;

    private VendorDataMapper $vendorDataMapper;

    /**
     * @var array<string, string>
     */
    private array $frameworksVendorToName = [];

    private ParametersConfigDumper $parametersConfigDumper;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        VendorDataFactory $vendorDataFactory,
        VendorDataMapper $vendorDataMapper,
        ParameterProvider $parameterProvider,
        ParametersConfigDumper $parametersConfigDumper
   ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->vendorDataFactory = $vendorDataFactory;
        $this->vendorDataMapper = $vendorDataMapper;
        $this->frameworksVendorToName = $parameterProvider->provideArrayParameter(
            Option::FRAMEWORKS_VENDOR_TO_NAME
        );
        $this->parametersConfigDumper = $parametersConfigDumper;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Generates downloads stats data for MVC PHP vendors');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $vendorsData = $this->vendorDataFactory->createVendorsData($this->frameworksVendorToName);

        foreach ($vendorsData[self::VENDORS] as $key => $vendorData) {
            $vendorsData[self::VENDORS][$key] = $this->vendorDataMapper->mapObjectToArray($vendorData);
        }

        $fileInfo = $this->parametersConfigDumper->dumpPhp(Option::PHP_FRAMEWORK_TRENDS, $vendorsData);

        $message = sprintf(
            'Data for %d frameworks dumped into" %s" file',
            count($vendorsData[self::VENDORS]),
            $fileInfo->getRelativeFilePathFromCwd()
        );
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}
