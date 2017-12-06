<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher;

use Symplify\Statie\Renderable\File\PostFile;
use TomasVotruba\Website\TweetPublisher\Exception\TweetImageNotFoundException;
use TomasVotruba\Website\TweetPublisher\Exception\TweetTooLongException;

final class PostTweetsProvider
{
    /**
     * @var int
     */
    private const TWEET_MAX_LENGTH = 280;

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
     * @return string[][]
     */
    public function provide(): array
    {
        $postTweets = [];
        foreach ($this->postsProvider->provide() as $post) {
            $postConfiguration = $post->getConfiguration();
            if (! isset($postConfiguration['tweet'])) {
                continue;
            }

            $postTweet = $this->appendAbsoluteUrlToTweet($post, $postConfiguration);
            $this->ensureTweetFitsAllowedLength($postConfiguration['tweet'], $post);

            $tweetImage = $this->resolveTweetImage($post, $postConfiguration);

            $postTweets[] = [
                'text' => $postTweet,
                'image' => $tweetImage,
            ];
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

    /**
     * @param mixed[] $postConfiguration
     */
    private function appendAbsoluteUrlToTweet(PostFile $postFile, array $postConfiguration): string
    {
        $url = $this->getAbsoluteUrlForPost($postFile);

        return $postConfiguration['tweet'] . ' ' . $url . '/';
    }

    private function getAbsoluteUrlForPost(PostFile $postFile): string
    {
        return $this->siteUrl . '/' . $postFile->getRelativeUrl();
    }

    /**
     * @param mixed[] $postConfiguration
     */
    private function resolveTweetImage(PostFile $postFile, array $postConfiguration): ?string
    {
        if (! isset($postConfiguration['tweet_image'])) {
            return null;
        }

        $sourceDirectory = __DIR__ . '/../../source/';
        $localFilePath = $sourceDirectory . $postConfiguration['tweet_image'];

        $this->ensureTweetImageExists($postFile, $localFilePath);

        return $this->siteUrl . '/' . $postConfiguration['tweet_image'];
    }

    private function ensureTweetImageExists(PostFile $postFile, string $localFilePath): void
    {
        if (! file_exists($localFilePath)) {
            throw new TweetImageNotFoundException(sprintf(
                'Tweet image "%s" for "%s" file not found. Check "tweet_image" option.',
                $localFilePath,
                realpath($postFile->getFilePath())
            ));
        }
    }
}
