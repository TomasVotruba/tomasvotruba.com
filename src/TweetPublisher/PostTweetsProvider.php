<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher;

use Symplify\Statie\Renderable\File\PostFile;
use TomasVotruba\Website\TweetPublisher\Exception\TweetTooLongException;

final class PostTweetsProvider
{
    /**
     * @var int
     */
    private const TWEET_MAX_LENGTH = 140;

    /**
     * @var int
     * @see https://dev.twitter.com/basics/tco#how-do-i-calculate-if-a-tweet-with-a-link-is-going-to-be-over-140-characters-or-not
     */
    private const SHORTENED_URL_LENGTH = 23;

    /**
     * @var string
     */
    private $siteUrl;

    /**
     * @var PostsProvider
     */
    private $postsProvider;

    public function __construct(string $siteUrl, PostsProvider $postsProvider)
    {
        $this->siteUrl = $siteUrl;
        $this->postsProvider = $postsProvider;
    }

    /**
     * @todo Make sure the order is from the newest to the oldest, like Twitter API.
     * @return string[]
     */
    public function provide(): array
    {
        $postTweets = [];
        foreach ($this->postsProvider->provide() as $post) {
            $postConfiguration = $post->getConfiguration();
            if (! isset($postConfiguration['tweet'])) {
                continue;
            }

            $url = $this->getAbsoluteUrlForPost($post);
            $postTweet = $postConfiguration['tweet'] . ' ' . $url . '/';
            $this->ensureTweetFitsAllowedLength($postConfiguration['tweet'], $post);

            $postTweets[] = $postTweet;
        }

        return $postTweets;
    }

    private function ensureTweetFitsAllowedLength(string $tweet, PostFile $postFile): void
    {
        $tweetLength = mb_strlen($tweet);
        if ($tweetLength <= self::TWEET_MAX_LENGTH) {
            return;
        }

        throw new TweetTooLongException(sprintf(
            'Tweet message "%s" is too long, after adding its url. It has %d chars, shorten it under %d.' .
                PHP_EOL .
                PHP_EOL .
                'Look to "%s" file.',
            $tweet,
            $tweetLength,
            self::TWEET_MAX_LENGTH - self::SHORTENED_URL_LENGTH,
            realpath($postFile->getFilePath())
        ));
    }

    private function getAbsoluteUrlForPost(PostFile $postFile): string
    {
        return $this->siteUrl . '/' . $postFile->getRelativeUrl();
    }
}
