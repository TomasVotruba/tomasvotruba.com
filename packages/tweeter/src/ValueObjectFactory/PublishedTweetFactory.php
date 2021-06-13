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
        $tweets = [];
        foreach ($rawTweets as $rawTweet) {
            $text = trim($rawTweet['text']);
            $createdAt = DateTime::from($rawTweet['created_at']);
            $link = $rawTweet['entities']['urls'][0]['expanded_url'] ?? null;

            $tweets[] = new PublishedTweet($text, $createdAt, $rawTweet['id'], $link);
        }

        return $tweets;
    }
}
