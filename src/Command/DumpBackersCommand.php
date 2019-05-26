<?php declare(strict_types=1);

namespace TomasVotruba\Website\Command;

use GuzzleHttp\Client;
use Nette\Utils\Json;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\Statie\FileSystem\GeneratedFilesDumper;
use TomasVotruba\Website\Result\VendorDataFactory;

final class DumpBackersCommand extends Command
{
    /**
     * @var string
     */
    private $patreonToken;

    /**
     * @var string
     */
    private const PATREON_BACKERS = 'https://api.patreon.com/api/oauth2/v2/campaigns/2703134/members';

    /**
     * @var string
     */
    private const PATREON_BACKER_DETAIL = 'https://api.patreon.com/api/oauth2/v2/members/%s?fields[member]=full_name';

    public function __construct(
        string $patreonToken,
        SymfonyStyle $symfonyStyle,
        GeneratedFilesDumper $generatedFilesDumper
    ) {
        parent::__construct();
        $this->symfonyStyle = $symfonyStyle;
        $this->generatedFilesDumper = $generatedFilesDumper;
        $this->patreonToken = $patreonToken;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Generates Retctor Patreon backers details to YAML');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = new Client();
        $options['headers']['Authorization'] = 'Bearer ' . $this->patreonToken;
        $response = $client->request('GET', self::PATREON_BACKERS, $options);
        $content = (string)$response->getBody();
        $json = Json::decode($content, Json::FORCE_ARRAY);

        foreach ($json['data'] as $backer) {
            $url = sprintf(self::PATREON_BACKER_DETAIL, $backer['id']);

            $response = $client->request('GET', $url, $options);
            $content = (string)$response->getBody();
            $json = Json::decode($content, Json::FORCE_ARRAY);

            dump(trim($json['data']['attributes']['full_name']));
        }

//        $this->generatedFilesDumper->dump('php_framework_trends', $vendorData);
//        $this->symfonyStyle->success('Data imported!');

        return ShellCode::SUCCESS;
    }
}
