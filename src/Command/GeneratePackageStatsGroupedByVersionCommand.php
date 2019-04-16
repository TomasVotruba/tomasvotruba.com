<?php declare(strict_types=1);

namespace TomasVotruba\Website\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\Statie\FileSystem\GeneratedFilesDumper;
use TomasVotruba\Website\Packagist\VendorPackagesProvider;
use TomasVotruba\Website\Result\PackageDataGroupedByVersionFactory;

final class GeneratePackageStatsGroupedByVersionCommand extends Command
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
     * @var GeneratedFilesDumper
     */
    private $generatedFilesDumper;

    /**
     * @var PackageDataGroupedByVersionFactory
     */
    private $packageDataGroupedByVersionFactory;

    /**
     * @var VendorPackagesProvider
     */
    private $vendorPackagesProvider;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        GeneratedFilesDumper $generatedFilesDumper,
        PackageDataGroupedByVersionFactory $packageDataGroupedByVersionFactory,
        VendorPackagesProvider $vendorPackagesProvider,
        array $frameworksVendorToName
    ) {
        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
        $this->generatedFilesDumper = $generatedFilesDumper;
        $this->packageDataGroupedByVersionFactory = $packageDataGroupedByVersionFactory;
        $this->vendorPackagesProvider = $vendorPackagesProvider;
        $this->frameworksVendorToName = $frameworksVendorToName;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Generates downloads stats data for MVC PHP vendors grouped by version');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyStyle->note('This is ok?');

        $vendorData = [];
        foreach ($this->frameworksVendorToName as $vendor => $name) {
            $vendorPackages = $this->vendorPackagesProvider->provideForVendor($vendor);
            $vendorData[$vendor]['packages_data'] = $this->packageDataGroupedByVersionFactory->createPackagesData(
                $vendorPackages
            );
            $vendorData['name'] = $name;
        }

        $this->generatedFilesDumper->dump('vendor_packages_by_version', $vendorData);
        $this->symfonyStyle->success('Data imported!');

        return ShellCode::SUCCESS;
    }
}
