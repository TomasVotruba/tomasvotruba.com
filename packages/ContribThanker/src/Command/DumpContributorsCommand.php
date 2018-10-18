<?php declare(strict_types=1);

namespace TomasVotruba\ContribThanker\Command;

use Nette\Utils\DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use TomasVotruba\ContribThanker\Api\GithubApi;

final class DumpContributorsCommand extends Command
{
    /**
     * @var string
     */
    private const CONTRIBUTORS_FILE = __DIR__ . '/../../../../source/_data/contributors.yml';

    /**
     * @var GithubApi
     */
    private $githubApi;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(GithubApi $githubApi, Filesystem $filesystem)
    {
        parent::__construct();
        $this->githubApi = $githubApi;
        $this->filesystem = $filesystem;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Generate list of contributors');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $data['parameters']['contributors'] = $this->githubApi->getContributors();

        $yamlDump = Yaml::dump($data, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
        $timestampComment = sprintf(
            '# this file was generated on %s, do not edit it manually' . PHP_EOL,
            (new DateTime())->format('Y-m-d H:i:s')
        );

        $this->filesystem->dumpFile(self::CONTRIBUTORS_FILE, $timestampComment . $yamlDump);
    }
}
