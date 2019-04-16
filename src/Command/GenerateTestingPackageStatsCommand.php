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
use TomasVotruba\Website\Result\PackageDataFactory;

final class GenerateTestingPackageStatsCommand extends Command
{
    /**
     * @var string[]
     */
    private $testingPackageNames = [];

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var GeneratedFilesDumper
     */
    private $generatedFilesDumper;

    /**
     * @var PackageDataFactory
     */
    private $packageDataFactory;

    /**
     * @param string[] $testingPackageNames
     */
    public function __construct(
        SymfonyStyle $symfonyStyle,
        GeneratedFilesDumper $generatedFilesDumper,
        PackageDataFactory $packageDataFactory,
        array $testingPackageNames
    ) {
        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
        $this->generatedFilesDumper = $generatedFilesDumper;
        $this->testingPackageNames = $testingPackageNames;
        $this->packageDataFactory = $packageDataFactory;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Generates downloads stats data for testing frameworks');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = [
            'packages' => $this->packageDataFactory->createPackagesData($this->testingPackageNames),
            'updated_at' => (new DateTime())->format('Y-m-d H:i:s'),
        ];

        $this->generatedFilesDumper->dump('testing_packages', $data);
        $this->symfonyStyle->success('Data imported!');

        return ShellCode::SUCCESS;
    }
}
