<?php declare(strict_types=1);

namespace TomasVotruba\Website\Patreon;

use GuzzleHttp\Client;
use Nette\Utils\Json;

final class PatreonApi
{
    /**
     * @see https://docs.patreon.com/#get-api-oauth2-v2-campaigns-campaign_id-members
     * @var string
     */
    private const PATREON_BACKERS_ENDPOINT = 'https://api.patreon.com/api/oauth2/v2/campaigns/2703134/members';

    /**
     * @see https://docs.patreon.com/#get-api-oauth2-v2-members-id
     * @var string
     */
    private const PATREON_BACKER_DETAIL_ENDPOINT = 'https://api.patreon.com/api/oauth2/v2/members/%s?fields[member]=full_name,patron_status,currently_entitled_amount_cents';

    /**
     * @var string
     */
    private const ACTIVE_PATRON = 'active_patron';

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
     * @return array<int, array<int,string>>
     */
    public function getBackesNamesGroupedByPaidAmount(): array
    {
        $backers = $this->callUrlToJson(self::PATREON_BACKERS_ENDPOINT);

        $backerNamesByAmount = [];

        foreach ($backers as $backer) {
            $backerDetailEndpoint = sprintf(self::PATREON_BACKER_DETAIL_ENDPOINT, $backer['id']);
            $backerDetail = $this->callUrlToJson($backerDetailEndpoint);

            // only active ones
            if ($backerDetail['attributes']['patron_status'] !== self::ACTIVE_PATRON) {
                continue;
            }

            $paidAmount = $backerDetail['attributes']['currently_entitled_amount_cents'];
            $paidAmount /= 100; // convert cents to dollars

            /** @var string $name */
            $name = $backerDetail['attributes']['full_name'];

            $backerNamesByAmount[$paidAmount][] = $name;
        }

        // sort from best payers
        krsort($backerNamesByAmount);

        return $backerNamesByAmount;
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
