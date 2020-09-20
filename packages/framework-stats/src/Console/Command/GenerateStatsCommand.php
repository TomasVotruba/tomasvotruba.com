<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\FrameworkStats\Mapper\VendorDataMapper;
use TomasVotruba\FrameworkStats\Result\VendorDataFactory;
use TomasVotruba\FrameworkStats\ValueObject\Option;
use TomasVotruba\FrameworkStats\Yaml\YamlFileDumper;

final class GenerateStatsCommand extends Command
{
    /**
     * @var string
     */
    private const PARAMETER_KEY = 'php_framework_trends';

    /**
     * @var string
     */
    private const GENERATED_FILE_OUTPUT_PATH = __DIR__ . '/../../../../../config/_data/generated/php_framework_trends.yaml';

    private YamlFileDumper $yamlFileDumper;

    private SymfonyStyle $symfonyStyle;

    private VendorDataFactory $vendorDataFactory;

    private VendorDataMapper $vendorDataMapper;

    /**
     * @var array<string, string>
     */
    private array $frameworksVendorToName = [];

    public function __construct(
        SymfonyStyle $symfonyStyle,
        YamlFileDumper $yamlFileDumper,
        VendorDataFactory $vendorDataFactory,
        VendorDataMapper $vendorDataMapper,
        ParameterProvider $parameterProvider
   ) {
        parent::__construct();

        $this->symfonyStyle = $symfonyStyle;
        $this->yamlFileDumper = $yamlFileDumper;
        $this->vendorDataFactory = $vendorDataFactory;
        $this->vendorDataMapper = $vendorDataMapper;
        $this->frameworksVendorToName = $parameterProvider->provideArrayParameter(Option::FRAMEWORKS_VENDOR_TO_NAME);
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Generates downloads stats data for MVC PHP vendors');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $vendorsData = $this->vendorDataFactory->createVendorsData($this->frameworksVendorToName);

        foreach ($vendorsData['vendors'] as $key => $vendorData) {
            $vendorsData['vendors'][$key] = $this->vendorDataMapper->mapObjectToArray($vendorData);
        }

        $this->yamlFileDumper->dumpAsParametersToFile(
            self::PARAMETER_KEY,
            $vendorsData,
            self::GENERATED_FILE_OUTPUT_PATH
        );

        $this->symfonyStyle->success('Data imported!');

        return ShellCode::SUCCESS;
    }
}
