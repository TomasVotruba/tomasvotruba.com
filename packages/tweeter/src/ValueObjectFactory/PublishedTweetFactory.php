<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\ValueObjectFactory;

use Nette\Utils\DateTime;
use TomasVotruba\Tweeter\ValueObject\PublishedTweet;

final class PublishedTweetFactory
{
    /**
     * @return PublishedTweet[]
     */
    public function createFromRawTweets(array $rawTweets): array
    {
        $tweets = [];
        foreach ($rawTweets as $fullTweet) {
            $createdAt = DateTime::from($fullTweet['created_at']);

            $text = trim($fullTweet['text']);
            $tweets[] = new PublishedTweet($text, $createdAt, $fullTweet['id']);
        }

        return $tweets;
    }
}
