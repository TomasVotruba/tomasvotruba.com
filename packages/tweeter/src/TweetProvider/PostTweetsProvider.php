<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetProvider;

use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Blog\ValueObject\Post;
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
        $postTweets = [];

        foreach ($this->postRepository->getPosts() as $post) {
            if (! $post->hasTweet()) {
                continue;
            }

            $tweetText = $this->resolveTweetText($post);

            $postTweets[] = $this->createPostTweet($post, $tweetText, $post->getTweetImage());
        }

        return $postTweets;
    }

    private function createPostTweet(Post $post, string $tweetText, ?string $tweetImage): PostTweet
    {
        return new PostTweet(
            $post->getId(),
            $tweetText,
            $post->getDateTime(),
            $tweetImage,
            $post->getAbsoluteUrl()
        );
    }

    private function resolveTweetText(Post $post): string
    {
        /** @var string $tweetText */
        $tweetText = $post->getTweetText();
        $this->tweetGuard->ensureTweetFitsAllowedLength($tweetText);

        return $tweetText . PHP_EOL . $post->getAbsoluteUrl();
    }
}
