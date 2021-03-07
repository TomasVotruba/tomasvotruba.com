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
            $createdAt = DateTime::from($rawTweet['created_at']);

            $text = trim($rawTweet['text']);
            $tweets[] = new PublishedTweet($text, $createdAt, $rawTweet['id']);
        }

        return $tweets;
    }
}
