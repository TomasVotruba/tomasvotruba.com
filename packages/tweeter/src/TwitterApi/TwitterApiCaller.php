<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TwitterApi;

use Nette\Utils\Json;
use TomasVotruba\Tweeter\Exception\TwitterApi\TwitterApiException;
use TwitterAPIExchange;

final class TwitterApiCaller
{
    public function __construct(
        private readonly TwitterAPIExchange $twitterAPIExchange
    ) {
    }

    /**
     * @param mixed[] $data
     * @return mixed[]
     */
    public function callPost(string $endPoint, array $data): array
    {
        $jsonResponse = $this->getTwitterApiExchange()
            ->setPostfields($data)
            ->buildOauth($endPoint, 'POST')
            ->performRequest();

        if ($jsonResponse === '') {
            return [];
        }

        $json = $this->decodeJson($jsonResponse);

        $this->ensureNoError($json);

        return $json;
    }

    /**
     * @param array<string, mixed> $data
     * @return mixed[]
     */
    public function callGet(string $endPoint, string $query, array $data): array
    {
        $data['q'] = $query;

        $getfield = '?' . http_build_query($data);

        $jsonResponse = $this->getTwitterApiExchange()
            ->setGetfield($getfield)
            ->buildOauth($endPoint, 'GET')
            ->performRequest();

        $json = $this->decodeJson($jsonResponse);
        $this->ensureNoError($json);

        return $json;
    }

    /**
     * @param array<string, mixed> $result
     */
    private function ensureNoError(array $result): void
    {
        if (! isset($result['errors']) && ! isset($result['error'])) {
            return;
        }

        /** @var string $errorMessage */
        $errorMessage = $result['errors'][0]['message'] ?? $result['error'];

        throw new TwitterApiException(sprintf('Twitter API failed due to: "%s"', $errorMessage));
    }

    /**
     * The clone is needed, because setting setGetfield() prevents using setPostfields()
     */
    private function getTwitterApiExchange(): TwitterAPIExchange
    {
        return clone $this->twitterAPIExchange;
    }

    /**
     * @return mixed[]
     */
    private function decodeJson(string $jsonResponse): array
    {
        return Json::decode($jsonResponse, Json::FORCE_ARRAY);
    }
}
