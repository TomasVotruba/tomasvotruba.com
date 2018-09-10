<?php declare(strict_types=1);

namespace TomasVotruba\ContribThanker\Api;

use GuzzleHttp\Client;
use TomasVotruba\ContribThanker\Guzzle\ResponseFormatter;

final class GithubApi
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var ResponseFormatter
     */
    private $responseFormatter;

    /**
     * @var string
     */
    private $repositoryName;

    /**
     * @var string
     */
    private $authorName;

    /**
     * @var mixed[]
     */
    private $options = [];

    public function __construct(
        Client $client,
        ResponseFormatter $responseFormatter,
        string $repositoryName,
        string $authorName,
        ?string $githubToken
    ) {
        $this->client = $client;
        $this->responseFormatter = $responseFormatter;
        $this->repositoryName = $repositoryName;
        $this->authorName = $authorName;

        if ($githubToken) {
            $this->options['headers']['Authorization'] = 'token ' . $githubToken;
        }
    }

    /**
     * @return mixed[]
     */
    public function getContributors(): array
    {
        $url = sprintf('https://api.github.com/repos/%s/contributors', $this->repositoryName);
        $response = $this->client->request('GET', $url, $this->options);
        $json = $this->responseFormatter->formatResponseToJson($response, $url);

        $contributors = [];
        foreach ($json as $item) {
            // skip ego
            if ($item['login'] === $this->authorName) {
                continue;
            }

            $contributors[] = [
                'name' => $item['login'],
                'url' => $item['html_url'],
                'contribution_count' => $item['contributions'],
            ];
        }

        return $contributors;
    }
}
