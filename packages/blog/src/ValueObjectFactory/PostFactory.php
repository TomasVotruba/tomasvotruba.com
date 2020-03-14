<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\ValueObjectFactory;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use ParsedownExtra;
use Symfony\Component\Yaml\Yaml;
use Symplify\SmartFileSystem\SmartFileInfo;
use TomasVotruba\Blog\FileSystem\PathAnalyzer;
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

    public function __construct(ParsedownExtra $parsedownExtra, PathAnalyzer $pathAnalyzer)
    {
        $this->parsedownExtra = $parsedownExtra;
        $this->pathAnalyzer = $pathAnalyzer;
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

        $tweetText = $configuration['tweet_text'] ?? null;
        $tweetImage = $configuration['tweet_image'] ?? null;

        $updatedAt = isset($configuration['updated_since']) ? DateTime::from($configuration['updated_since']) : null;
        $updatedMessage = $configuration['updated_message'] ?? null;

        $deprecatedAt = isset($configuration['deprecated_since']) ? DateTime::from(
            $configuration['deprecated_since']
        ) : null;
        $deprecatedMessage = $configuration['deprecated_message'] ?? null;

        $isTested = (bool) isset($configuration['tested']);

        $language = $configuration['lang'] ?? null;

        $testSlug = $configuration['test_slug'] ?? null;
        $sourceRelativePath = $this->getSourceRelativePath($smartFileInfo);

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
            $language
        );
    }

    private function getSourceRelativePath(SmartFileInfo $smartFileInfo): string
    {
        $relativeFilePath = $smartFileInfo->getRelativeFilePath();
        return ltrim($relativeFilePath, './');
    }
}
