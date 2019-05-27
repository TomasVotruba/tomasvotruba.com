<?php declare(strict_types=1);

namespace TomasVotruba\Website\Patreon;

use GuzzleHttp\Client;
use Nette\Utils\Json;

final class PatreonApi
{
    /**
     * @var string
     */
    private const PATREON_BACKERS_ENDPOINT = 'https://api.patreon.com/api/oauth2/v2/campaigns/2703134/members';

    /**
     * @var string
     */
    private const PATREON_BACKER_DETAIL_ENDPOINT = 'https://api.patreon.com/api/oauth2/v2/members/%s?fields[member]=full_name';

    /**
     * @var mixed[]
     */
    private $options = [];

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client, string $patreonToken)
    {
        $this->client = $client;
        $this->options['headers']['Authorization'] = 'Bearer ' . $patreonToken;
    }

    /**
     * @return string[]
     */
    public function getProjectBackersNames(): array
    {
        $backers = $this->callUrlToJson(self::PATREON_BACKERS_ENDPOINT);

        $backerNames = [];

        foreach ($backers as $backer) {
            $backerDetailEndpoint = sprintf(self::PATREON_BACKER_DETAIL_ENDPOINT, $backer['id']);
            $backerDetail = $this->callUrlToJson($backerDetailEndpoint);
            $backerNames[] = $backerDetail['attributes']['full_name'];
        }

        return $backerNames;
    }

    /**
     * @return mixed[]
     */
    private function callUrlToJson(string $url): array
    {
        $response = $this->client->request('GET', $url, $this->options);

        $content = (string) $response->getBody();

        $data = Json::decode($content, Json::FORCE_ARRAY);

        return $data['data'] ?? [];
    }
}
