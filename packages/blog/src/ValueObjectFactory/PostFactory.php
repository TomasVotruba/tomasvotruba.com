<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\ValueObjectFactory;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use ParsedownExtra;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Yaml\Yaml;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;
use TomasVotruba\Blog\FileSystem\PathAnalyzer;
use TomasVotruba\Blog\Testing\TestedPostAnalyzer;
use TomasVotruba\Blog\ValueObject\Post;
use TomasVotruba\FrameworkStats\Exception\ShouldNotHappenException;

final class PostFactory
{
    /**
     * @var string
     */
    private const SLASHES_WITH_SPACES_PATTERN = '(?:---[\s]*[\r\n]+)';

    /**
     * @var string
     */
    private const CONFIG_CONTENT_PATTERN = '#^\s*' . self::SLASHES_WITH_SPACES_PATTERN . '?(?<config>.*?)' . self::SLASHES_WITH_SPACES_PATTERN . '(?<content>.*?)$#s';

    private ParsedownExtra $parsedownExtra;

    private PathAnalyzer $pathAnalyzer;

    private RouterInterface $router;

    private string $projectDir;

    private string $siteUrl;

    private FileSystemGuard $fileSystemGuard;

    private TestedPostAnalyzer $testedPostAnalyzer;

    public function __construct(
        ParsedownExtra $parsedownExtra,
        PathAnalyzer $pathAnalyzer,
        RouterInterface $router,
        TestedPostAnalyzer $testedPostAnalyzer,
        string $siteUrl,
        string $projectDir,
        FileSystemGuard $fileSystemGuard
    ) {
        $this->parsedownExtra = $parsedownExtra;
        $this->pathAnalyzer = $pathAnalyzer;
        $this->router = $router;
        $this->siteUrl = rtrim($siteUrl, '/');
        $this->projectDir = $projectDir;
        $this->fileSystemGuard = $fileSystemGuard;
        $this->testedPostAnalyzer = $testedPostAnalyzer;
    }

    public function createFromFileInfo(SmartFileInfo $smartFileInfo): Post
    {
        $matches = Strings::match($smartFileInfo->getContents(), self::CONFIG_CONTENT_PATTERN);

        if (! isset($matches['config'])) {
            throw new ShouldNotHappenException();
        }

        $configuration = Yaml::parse($matches['config']);

        $id = $configuration['id'];
        $title = $configuration['title'];
        $perex = $configuration['perex'];

        $slug = $this->pathAnalyzer->getSlug($smartFileInfo);

        $dateTime = $this->pathAnalyzer->detectDate($smartFileInfo);
        if ($dateTime === null) {
            throw new ShouldNotHappenException();
        }

        if (! isset($matches['content'])) {
            throw new ShouldNotHappenException();
        }
        $htmlContent = $this->parsedownExtra->parse($matches['content']);

        $tweetText = $configuration['tweet'] ?? null;
        $tweetImage = $this->resolveTweetImage($configuration);

        $updatedAt = isset($configuration['updated_since']) ? DateTime::from($configuration['updated_since']) : null;
        $updatedMessage = $configuration['updated_message'] ?? null;

        $deprecatedAt = isset($configuration['deprecated_since']) ? DateTime::from(
            $configuration['deprecated_since']
        ) : null;
        $deprecatedMessage = $configuration['deprecated_message'] ?? null;

        $isTested = $this->testedPostAnalyzer->isPostIdTested($id);

        $language = $configuration['lang'] ?? null;

        $testSlug = $configuration['test_slug'] ?? null;
        $sourceRelativePath = $this->getSourceRelativePath($smartFileInfo);

        $absoluteUrl = $this->createAbsoluteUrl($slug);

        return new Post(
            $id,
            $title,
            $slug,
            $dateTime,
            $perex,
            $htmlContent,
            $tweetText,
            $tweetImage,
            $updatedAt,
            $updatedMessage,
            $isTested,
            $testSlug,
            $sourceRelativePath,
            $deprecatedAt,
            $deprecatedMessage,
            $language,
            $absoluteUrl
        );
    }

    private function resolveTweetImage(array $configuration): ?string
    {
        $tweetImage = $configuration['tweet_image'] ?? null;
        if ($tweetImage === null) {
            return null;
        }

        $tweetImage = ltrim($tweetImage, '/');
        $localTweetImagePath = $this->projectDir . '/public/' . $tweetImage;

        $this->fileSystemGuard->ensureFileExists($localTweetImagePath, __METHOD__);

        return $this->siteUrl . '/' . $tweetImage;
    }

    private function getSourceRelativePath(SmartFileInfo $smartFileInfo): string
    {
        $relativeFilePath = $smartFileInfo->getRelativeFilePath();
        return ltrim($relativeFilePath, './');
    }

    private function createAbsoluteUrl(string $slug): string
    {
        $siteUrl = rtrim($this->siteUrl, '/');

        return $siteUrl . $this->router->generate('post_detail', ['slug' => $slug]);
    }
}
