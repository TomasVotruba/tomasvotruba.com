<?php declare(strict_types=1);

namespace TomasVotruba\Website\GoogleAnalyticsPostViews;

use Google_Client;

final class PostViewsFetcher
{
    /**
     * @var Google_Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName("Client_Library_Examples");
        $this->client->setDeveloperKey("YOUR_APP_KEY");
    }

    public function run()
    {
        dump($this->client);
        //
        dump('AA');
        die;
    }
}
