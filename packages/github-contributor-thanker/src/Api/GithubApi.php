<?php

declare(strict_types=1);

namespace TomasVotruba\GithubContributorsThanker\Api;

use Symplify\PackageBuilder\Http\BetterGuzzleClient;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\GithubContributorsThanker\ValueObject\Option;

final class GithubApi
{
    /**
     * Better detailed URL - the more than top 30
     * @see https://developer.github.com/v3/repos/statistics/#get-contributors-list-with-additions-deletions-and-commit-counts
     * @var string
     */
    private const API_CONTRIBUTORS = 'https://api.github.com/repos/%s/stats/contributors';

    /**
     * @var string
     */
    private const AUTHOR = 'author';

    private string $thankerRepositoryName;

    private string $thankerAuthorName;

    private BetterGuzzleClient $betterGuzzleClient;

    public function __construct(BetterGuzzleClient $betterGuzzleClient, ParameterProvider $parameterProvider)
    {
        $this->thankerRepositoryName = $parameterProvider->provideStringParameter(Option::THANKER_REPOSITORY_NAME);
        $this->thankerAuthorName = $parameterProvider->provideStringParameter(Option::THANKER_AUTHOR_NAME);
        $this->betterGuzzleClient = $betterGuzzleClient;
    }

    /**
     * @return mixed[]
     */
    public function getContributors(): array
    {
        $url = sprintf(self::API_CONTRIBUTORS, $this->thankerRepositoryName);
        $json = $this->betterGuzzleClient->requestToJson($url);

        // reverse to max → min
        rsort($json);

        $contributors = [];
        foreach ($json as $item) {
            // skip ego
            if ($item[self::AUTHOR]['login'] === $this->thankerAuthorName) {
                continue;
            }

            $contributors[] = [
                'name' => $item[self::AUTHOR]['login'],
                'url' => $item[self::AUTHOR]['html_url'],
                'photo' => $item[self::AUTHOR]['avatar_url'],
                'contribution_count' => $item['total'],
            ];
        }

        return $contributors;
    }
}
