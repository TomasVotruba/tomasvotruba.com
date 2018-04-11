<?php declare(strict_types=1);

namespace TomasVotruba\StatieTweetPublisher\TweetProvider;

use Symplify\Statie\Renderable\File\PostFile;
use TomasVotruba\StatieTweetPublisher\PostsProvider;
use TomasVotruba\StatieTweetPublisher\Tweet\Tweet;
use TomasVotruba\StatieTweetPublisher\TweetGuard;

final class PostTweetsProvider
{
    /**
     * @var string
     */
    private $siteUrl;

    /**
     * @var PostsProvider
     */
    private $postsProvider;

    /**
     * @var string
     */
    private $sourceDirectory;

    /**
     * @var TweetGuard
     */
    private $tweetGuard;

    public function __construct(
        string $siteUrl,
        string $sourceDirectory,
        PostsProvider $postsProvider,
        TweetGuard $tweetGuard
    ) {
        $this->siteUrl = $siteUrl;
        $this->postsProvider = $postsProvider;
        $this->sourceDirectory = $sourceDirectory;
        $this->tweetGuard = $tweetGuard;
    }

    /**
     * @return Tweet[]
     */
    public function provide(): array
    {
        $postTweets = [];
        foreach ($this->postsProvider->provide() as $post) {
            $postConfiguration = $post->getConfiguration();
            if (! isset($postConfiguration['tweet'])) {
                continue;
            }

            $rawTweetText = $postConfiguration['tweet'];
            $this->tweetGuard->ensureTweetFitsAllowedLength($rawTweetText, $post);

            $postTweet = $this->appendAbsoluteUrlToTweet($post, $rawTweetText);

            $tweetImage = $this->resolveTweetImage($post, $postConfiguration);
            $postTweets[] = Tweet::createFromTextAndImage($postTweet, $tweetImage);
        }

        return $postTweets;
    }

    private function appendAbsoluteUrlToTweet(PostFile $postFile, string $rawTweetText): string
    {
        $url = $this->getAbsoluteUrlForPost($postFile);

        return $rawTweetText . ' ' . $url . '/';
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

        $localFilePath = $this->sourceDirectory . $postConfiguration['tweet_image'];

        $this->tweetGuard->ensureTweetImageExists($postFile, $localFilePath);

        return $this->siteUrl . '/' . $postConfiguration['tweet_image'];
    }
}
