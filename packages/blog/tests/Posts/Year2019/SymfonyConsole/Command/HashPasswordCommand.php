<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Posts\Year2019\SymfonyConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\ShellCode;

final class HashPasswordCommand extends Command
{
    /**
     * @var string
     */
    private const ARGUMENT_PASSWORD = 'password';

    protected function configure(): void
    {
        $this->setName('hash-password');
        $this->setDescription('Hashes provided password with BCRYPT and prints to output.');
        $this->addArgument(self::ARGUMENT_PASSWORD, InputArgument::REQUIRED, 'Password to be hashed.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $password = (string) $input->getArgument(self::ARGUMENT_PASSWORD);

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $message = sprintf('Your hashed password is: <info>%s</info>', $hashedPassword);
        $output->writeln($message);

        return ShellCode::SUCCESS;
    }
}
