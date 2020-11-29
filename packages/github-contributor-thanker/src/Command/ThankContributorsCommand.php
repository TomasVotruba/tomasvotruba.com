<?php

declare(strict_types=1);

namespace TomasVotruba\GithubContributorsThanker\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use TomasVotruba\GithubContributorsThanker\Api\GithubApi;
use TomasVotruba\Website\FileSystem\ParametersConfigDumper;
use TomasVotruba\Website\ValueObject\Option;

final class ThankContributorsCommand extends Command
{
    private GithubApi $githubApi;

    private SymfonyStyle $symfonyStyle;

    private ParametersConfigDumper $generatedFilesDumper;

    public function __construct(
        GithubApi $githubApi,
        SymfonyStyle $symfonyStyle,
        ParametersConfigDumper $parametersConfigDumper
    ) {
        parent::__construct();

        $this->githubApi = $githubApi;
        $this->symfonyStyle = $symfonyStyle;
        $this->generatedFilesDumper = $parametersConfigDumper;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Dump "contributors.php" file with your Github repository contributors');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $contributors = $this->githubApi->getContributors();
        if (count($contributors) === 0) {
            $message = sprintf(
                'Found 0 contributions - stick with the current dump. Try running the same command with "ACCESS_TOKEN=xxx bin/console ...". Get your token here: %s',
                'https://github.com/settings/tokens/new'
            );
            $this->symfonyStyle->warning($message);

            return ShellCode::SUCCESS;
        }

        $dumpFileInfo = $this->generatedFilesDumper->dumpPhp(Option::CONTRIBUTORS, $contributors);

        $successMessage = sprintf(
            'Dumped %d contributors to "%s" file',
            count($contributors),
            $dumpFileInfo->getRelativeFilePathFromCwd()
        );
        $this->symfonyStyle->success($successMessage);

        return ShellCode::SUCCESS;
    }
}
