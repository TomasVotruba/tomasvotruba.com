<?php declare(strict_types=1);

namespace TomasVotruba\Website\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\Statie\FileSystem\GeneratedFilesDumper;
use TomasVotruba\Website\Patreon\PatreonApi;

final class DumpBackersCommand extends Command
{
    /**
     * @var PatreonApi
     */
    private $patreonApi;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var GeneratedFilesDumper
     */
    private $generatedFilesDumper;

    public function __construct(
        PatreonApi $patreonApi,
        SymfonyStyle $symfonyStyle,
        GeneratedFilesDumper $generatedFilesDumper
    ) {
        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
        $this->generatedFilesDumper = $generatedFilesDumper;
        $this->patreonApi = $patreonApi;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Generate Rector Patreon backers from https://patreon.com/rectorphp to YAML');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->patreonApi->getBackesNamesGroupedByPaidAmount();

        $this->generatedFilesDumper->dump('patreon_backers_by_paid_amount', $data);
        $this->symfonyStyle->success('Data imported!');

        return ShellCode::SUCCESS;
    }
}
