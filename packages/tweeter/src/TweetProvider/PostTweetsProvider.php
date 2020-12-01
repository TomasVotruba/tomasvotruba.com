<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TweetProvider;

use DateTimeInterface;
use Nette\Utils\DateTime;
use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Tweeter\Validation\TweetGuard;
use TomasVotruba\Tweeter\ValueObject\Tweet;

final class PostTweetsProvider
{
    private DateTimeInterface $maximumDateTimeLimit;

    public function __construct(private TweetGuard $tweetGuard, private PostRepository $postRepository)
    {
        $this->maximumDateTimeLimit = DateTime::from('2019-01-01');
    }

    /**
     * @return Tweet[]
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
            $tweetText .= PHP_EOL . PHP_EOL . $post->getAbsoluteUrl();

            $tweetImage = $post->getTweetImage();

            // skip way too old posts
            if ($post->getDateTime() < $this->maximumDateTimeLimit) {
                continue;
            }

            $tweets[] = new Tweet($tweetText, $post->getDateTime(), $tweetImage);
        }

        return $tweets;
    }
}
