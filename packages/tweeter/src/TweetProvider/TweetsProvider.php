<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetProvider;

use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Tweeter\Contract\TweetsProviderInterface;
use TomasVotruba\Tweeter\Validation\TweetGuard;
use TomasVotruba\Tweeter\ValueObject\Tweet;

final class TweetsProvider implements TweetsProviderInterface
{
    private TweetGuard $tweetGuard;

    private PostRepository $postRepository;

    public function __construct(TweetGuard $tweetGuard, PostRepository $postRepository)
    {
        $this->tweetGuard = $tweetGuard;
        $this->postRepository = $postRepository;
    }

    /**
     * @return Tweet[]
     */
    public function provide(): array
    {
        $tweets = [];

        foreach ($this->postRepository->fetchAll() as $post) {
            if (! $post->hasTweet()) {
                continue;
            }

            /** @var string $tweetText */
            $tweetText = $post->getTweetText();
            $this->tweetGuard->ensureTweetFitsAllowedLength($tweetText);
            $tweetText .= PHP_EOL . PHP_EOL . $post->getAbsoluteUrl();

            $tweetImage = $post->getTweetImage();

            $tweets[] = new Tweet($tweetText, $post->getDateTime(), $tweetImage);
        }

        return $tweets;
    }
}
