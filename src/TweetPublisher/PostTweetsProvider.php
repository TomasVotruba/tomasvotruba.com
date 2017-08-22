<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher;

use Nette\Utils\Finder;
use Symplify\Statie\Renderable\Configuration\ConfigurationDecorator;
use Symplify\Statie\Renderable\File\FileFactory;
use Symplify\Statie\Renderable\File\PostFile;
use Symplify\Statie\Renderable\Routing\RouteFileDecorator;
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
    private $postSource;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var ConfigurationDecorator
     */
    private $configurationDecorator;

    /**
     * @var RouteFileDecorator
     */
    private $routeFileDecorator;

    /**
     * @var string
     */
    private $siteUrl;

    public function __construct(
        string $postSource,
        string $siteUrl,
        FileFactory $fileFactory,
        ConfigurationDecorator $configurationDecorator,
        RouteFileDecorator $routeFileDecorator
    ) {
        $this->fileFactory = $fileFactory;
        $this->configurationDecorator = $configurationDecorator;
        $this->postSource = $postSource;
        $this->routeFileDecorator = $routeFileDecorator;
        $this->siteUrl = $siteUrl;
    }

    /**
     * @todo Make sure the order is from the newest to the oldest, like Twitter API.
     * @return string[]
     */
    public function provide(): array
    {
        $postTweets = [];
        foreach ($this->getPosts() as $post) {
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

    /**
     * @return PostFile[]
     */
    private function getPosts(): array
    {
        return $this->getPostsWithConfigurationFromSource($this->postSource);
    }

    /**
     * @return PostFile[]
     */
    private function getPostsWithConfigurationFromSource(string $postSource): array
    {
        $files = $this->findMdFilesInDirectory($postSource);
        $posts = $this->fileFactory->createFromFileInfos($files);
        $this->configurationDecorator->decorateFiles($posts);
        $this->routeFileDecorator->decorateFiles($posts);

        return $posts;
    }

    /**
     * @return string[]
     */
    private function findMdFilesInDirectory(string $postSource): array
    {
        /** @var Finder $finder */
        $finder = Finder::findFiles('*.md')->from($postSource);

        $files = [];
        foreach ($finder as $key => $file) {
            $files[$key] = $file;
        }

        return $files;
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

    private function getAbsoluteUrlForPost(PostFile $post): string
    {
        return $this->siteUrl . '/' . $post->getRelativeUrl();
    }
}
