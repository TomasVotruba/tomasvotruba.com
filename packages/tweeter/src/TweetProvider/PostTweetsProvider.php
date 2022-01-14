<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetProvider;

use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Tweeter\Validation\TweetGuard;
use TomasVotruba\Tweeter\ValueObject\PostTweet;

final class PostTweetsProvider
{
    public function __construct(
        private readonly TweetGuard $tweetGuard,
        private readonly PostRepository $postRepository
    ) {
    }

    /**
     * @return PostTweet[]
     */
    public function provide(): array
    {
        $tweets = [];

        foreach ($this->postRepository->getPosts() as $post) {
            if (! $post->hasTweet()) {
                continue;
            }

            /** @var string $tweetText */
            $tweetText = $post->getTweetText();
            $this->tweetGuard->ensureTweetFitsAllowedLength($tweetText);
            $tweetText .= PHP_EOL . $post->getAbsoluteUrl();

            $tweetImage = $post->getTweetImage();

            $tweets[] = new PostTweet(
                $post->getId(),
                $tweetText,
                $post->getDateTime(),
                $tweetImage,
                $post->getAbsoluteUrl()
            );
        }

        return $tweets;
    }
}
