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
use TomasVotruba\Website\Yaml\GeneratedFilesDumper;

final class ThankContributorsCommand extends Command
{
    private GithubApi $githubApi;

    private SymfonyStyle $symfonyStyle;

    private GeneratedFilesDumper $generatedFilesDumper;

    public function __construct(
        GithubApi $githubApi,
        SymfonyStyle $symfonyStyle,
        GeneratedFilesDumper $generatedFilesDumper
    ) {
        parent::__construct();

        $this->githubApi = $githubApi;
        $this->symfonyStyle = $symfonyStyle;
        $this->generatedFilesDumper = $generatedFilesDumper;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Dump contributors.yaml file with your Github repository contributors');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $contributors = $this->githubApi->getContributors();
        if (count($contributors) === 0) {
            $this->symfonyStyle->note('Found 0 contributions - stick with the current dump');

            return ShellCode::SUCCESS;
        }

        $this->generatedFilesDumper->dump('contributors', $contributors, 'yaml');

        $successMessage = sprintf('Dumped %d contributors', count($contributors));
        $this->symfonyStyle->success($successMessage);

        return ShellCode::SUCCESS;
    }
}
