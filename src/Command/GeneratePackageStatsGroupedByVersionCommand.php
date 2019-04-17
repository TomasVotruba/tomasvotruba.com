<?php declare(strict_types=1);

namespace TomasVotruba\Website\Command;

use Nette\Utils\DateTime;
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

    /**
     * @var string[]
     */
    private $versionedFrameworkVendor = [];

    /**
     * @param string[] $versionedFrameworkVendor
     */
    public function __construct(
        SymfonyStyle $symfonyStyle,
        GeneratedFilesDumper $generatedFilesDumper,
        PackageDataGroupedByVersionFactory $packageDataGroupedByVersionFactory,
        VendorPackagesProvider $vendorPackagesProvider,
        array $versionedFrameworkVendor
    ) {
        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
        $this->generatedFilesDumper = $generatedFilesDumper;
        $this->packageDataGroupedByVersionFactory = $packageDataGroupedByVersionFactory;
        $this->vendorPackagesProvider = $vendorPackagesProvider;
        $this->versionedFrameworkVendor = $versionedFrameworkVendor;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Generates downloads stats data for MVC PHP vendors grouped by version');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $vendorData = [];

        ksort($this->versionedFrameworkVendor);

        foreach ($this->versionedFrameworkVendor as $vendor => $name) {
            $this->symfonyStyle->title(sprintf('Loading packages data grouped by version for "%s" vendor', $vendor));

            $vendorPackages = $this->vendorPackagesProvider->provideForVendor($vendor);
            $vendorData[$vendor]['packages_data'] = $this->packageDataGroupedByVersionFactory->createPackagesData(
                $vendorPackages
            );

            $vendorData[$vendor]['name'] = $name;
        }

        $data['vendors'] = $vendorData;
        $data['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');

        $this->generatedFilesDumper->dump('vendor_packages_by_version', $data);
        $this->symfonyStyle->success('Data imported!');

        return ShellCode::SUCCESS;
    }
}
