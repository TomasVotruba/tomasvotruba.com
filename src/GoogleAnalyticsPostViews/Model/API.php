<?php declare(strict_types=1);

namespace TomasVotruba\Website\GoogleAnalyticsPostViews\Model;

use Nette\Configurator;
use Nette\Object;
use Nette\Utils\DateTime;
use Tracy\Debugger;

/**
 * Class API
 *
 * Tutorials:
 * - http://www.daimto.com/google-oauth2-php/
 * - https://developers.google.com/analytics/solutions/articles/hello-analytics-api
 *
 * API:
 * - https://developers.google.com/analytics/
 *
 * Common Queries:
 * - https://developers.google.com/analytics/devguides/reporting/core/v3/common-queries?csw=1
 *
 * Query Explorer:
 * - https://ga-dev-tools.appspot.com/explorer/
 *
 * @package App\Analytics
 */
class API extends Object
{
    /**
     * In some cases, your application may need to access a Google API when the user is not present.
     * Examples of this include backup services and applications that make blogger posts exactly at 8am on Monday morning.
     * This style of access is called offline, and web server applications may request offline access from a user.
     * The normal and default style of access is called online.
     */
    const ACCESS_OFFLINE = 'offline';

    const PRODUCTION = 'production';
    const DEVELOPMENT = 'development';

    /** @var \Google_Client */
    public $client;

    /** @var \Google_Service_Analytics */
    private $service = NULL;

    private $tokens = NULL;

    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    /** @var string */
    private $redirectUrl;

    /** @var string */
    private $developerKey;

    /** @var array */
    private $scopes = [];

    public function setClient()
    {
        $this->client = new \Google_Client();

        $this->client->setApplicationName('FBMAN.cz');
        $this->client->setClientId($this->clientId);
        $this->client->setClientSecret($this->clientSecret);
        $this->client->setRedirectUri($this->redirectUrl);
        $this->client->setDeveloperKey($this->developerKey);
        $this->client->setScopes($this->scopes);
        $this->client->setAccessType(self::ACCESS_OFFLINE);
    }

    public function getUsers($profileId, DateTime $from, DateTime $to)
    {
        if (is_null($this->service) || !($this->service instanceof \Google_Service_Analytics))
            $this->service = new \Google_Service_Analytics($this->client);

        $result = [];

        $data = $this->service->data_ga->get(
            'ga:' . $profileId,
            $from->format('Y-m-d'),
            $to->format('Y-m-d'),
            'ga:sessions,ga:pageviews,ga:users',
            [
                'dimensions' => 'ga:date'
            ]
        );

        if ($data->getRows() > 0) {
            foreach ($data->getRows() as $row) {
                $result[] = [
                    'date' => new DateTime($row[0]),
                    'visits' => (int)$row[1],
                    'pages' => (int)$row[2],
                    'users' => (int)$row[3]
                ];
            }
        }

        return $result;
    }

    public function addScope($scope)
    {
        array_push($this->scopes, $scope);
    }

    public function setConfig($config)
    {
        $google = Configurator::detectDebugMode() ? $config[self::DEVELOPMENT] : $config[self::PRODUCTION];

        $this->setClientId($google['client']['id']);
        $this->setClientSecret($google['client']['secret']);
        $this->setRedirectUrl($google['client']['url']);
        $this->setDeveloperKey($google['api']['key']);
    }

    public function setTokens($tokens)
    {
        $this->tokens = $tokens;
        $this->client->setAccessToken($this->tokens);
    }

    public function refreshToken($token)
    {
        $this->client->refreshToken($token);
        return $this->client->getAccessToken();
    }

    private function setClientId($id)
    {
        $this->clientId = $id;
    }

    private function setClientSecret($secret)
    {
        $this->clientSecret = $secret;
    }

    private function setRedirectUrl($url)
    {
        $this->redirectUrl = $url;
    }

    private function setDeveloperKey($key)
    {
        $this->developerKey = $key;
    }
}