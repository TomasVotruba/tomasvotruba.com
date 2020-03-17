<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Posts\Year2018\ConsoleDI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SomeCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('some');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('OK');

        // success code
        return 0;
    }
}
