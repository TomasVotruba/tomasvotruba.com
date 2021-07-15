<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\ValueObjectFactory;

use Nette\Utils\DateTime;
use TomasVotruba\Tweeter\ValueObject\PublishedTweet;

final class PublishedTweetFactory
{
    /**
     * @param mixed[] $rawTweets
     * @return PublishedTweet[]
     */
    public function createFromRawTweets(array $rawTweets): array
    {
        $postTweets = [];

        // put the latest on the bottom, so it's easier to dump
        $rawTweets = array_reverse($rawTweets);

        foreach ($rawTweets as $rawTweet) {
            $postUrl = $this->resolvePostUrl($rawTweet['entities']['urls']);
            if ($postUrl === null) {
                continue;
            }

            $text = trim($rawTweet['text']);
            $createdAt = DateTime::from($rawTweet['created_at']);

            $postTweets[] = new PublishedTweet($text, $createdAt, $rawTweet['id'], $postUrl);
        }

        return $postTweets;
    }

    /**
     * @param array<string, mixed> $urls
     */
    private function resolvePostUrl(array $urls): ?string
    {
        // not urls → skip
        if ($urls === []) {
            return null;
        }

        foreach ($urls as $url) {
            if (! str_starts_with($url['display_url'], 'tomasvotruba.com/blog')) {
                continue;
            }

            return $url['expanded_url'];
        }

        return null;
    }
}
