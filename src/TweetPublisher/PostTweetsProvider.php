<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher;

use Symplify\Statie\Renderable\File\PostFile;

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
            $this->tweetGuard->ensureTweetFitsAllowedLength($postConfiguration['tweet'], $post);

            $tweetImage = $this->resolveTweetImage($post, $postConfiguration);

            $postTweets[] = [
                'text' => $postTweet,
                'image' => $tweetImage,
            ];
        }

        return $postTweets;
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

        $localFilePath = $this->sourceDirectory . $postConfiguration['tweet_image'];

        $this->tweetGuard->ensureTweetImageExists($postFile, $localFilePath);

        return $this->siteUrl . '/' . $postConfiguration['tweet_image'];
    }
}
