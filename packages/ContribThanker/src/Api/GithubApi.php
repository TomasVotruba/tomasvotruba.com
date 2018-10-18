<?php declare(strict_types=1);

namespace TomasVotruba\ContribThanker\Api;

use GuzzleHttp\Client;
use TomasVotruba\ContribThanker\Guzzle\ResponseFormatter;

final class GithubApi
{
    /**
     * Better detailed URL - the more than top 30
     * @see https://developer.github.com/v3/repos/statistics/#get-contributors-list-with-additions-deletions-and-commit-counts
     * @var string
     */
    private const API_CONTRIBUTORS = 'https://api.github.com/repos/%s/stats/contributors';

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
        $url = sprintf(self::API_CONTRIBUTORS, $this->repositoryName);
        $json = $this->callRequestToJson($url);

        // reverse to max → min
        rsort($json);

        $contributors = [];
        foreach ($json as $item) {
            // skip ego
            if ($item['author']['login'] === $this->authorName) {
                continue;
            }

            $contributors[] = [
                'name' => $item['author']['login'],
                'url' => $item['author']['html_url'],
                'contribution_count' => $item['total'],
            ];
        }

        return $contributors;
    }

    /**
     * @return mixed[]
     */
    private function callRequestToJson(string $url): array
    {
        $response = $this->client->request('GET', $url, $this->options);

        return $this->responseFormatter->formatResponseToJson($response, $url);
    }
}
