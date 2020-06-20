<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2019\SymfonyConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class HashPasswordCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('hash-password');
        $this->setDescription('Hashes provided password with BCRYPT and prints to output.');
        $this->addArgument('password', InputArgument::REQUIRED, 'Password to be hashed.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $password = (string) $input->getArgument('password');

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $output->writeln(sprintf('Your hashed password is: <info>%s</info>', $hashedPassword));

        return 0;
    }
}
