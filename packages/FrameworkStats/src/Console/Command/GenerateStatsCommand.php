<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use TomasVotruba\FrameworkStats\Result\VendorDataFactory;
use TomasVotruba\FrameworkStats\Yaml\YamlFileDumper;

final class GenerateStatsCommand extends Command
{
    /**
     * @var string[]
     */
    private $frameworksVendorToName = [];

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var YamlFileDumper
     */
    private $yamlFileDumper;

    /**
     * @var VendorDataFactory
     */
    private $vendorDataFactory;

    /**
     * @param string[] $frameworksVendorToName
     */
    public function __construct(
        SymfonyStyle $symfonyStyle,
        YamlFileDumper $yamlFileDumper,
        VendorDataFactory $vendorDataFactory,
        array $frameworksVendorToName
    ) {
        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
        $this->yamlFileDumper = $yamlFileDumper;
        $this->vendorDataFactory = $vendorDataFactory;
        $this->frameworksVendorToName = $frameworksVendorToName;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Generates downloads stats data for MVC PHP vendors');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $vendorData = $this->vendorDataFactory->createVendorsData($this->frameworksVendorToName);

        $filePath = __DIR__ . '/../../../../../source/_data/generated/php_framework_trends.yaml';

        $this->yamlFileDumper->dumpAsParametersToFile('php_framework_trends', $vendorData, $filePath);

        $this->symfonyStyle->success('Data imported!');

        return ShellCode::SUCCESS;
    }
}
