<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\ValueObjectFactory;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use ParsedownExtra;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Yaml\Yaml;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;
use TomasVotruba\Blog\FileSystem\PathAnalyzer;
use TomasVotruba\Blog\Testing\TestedPostAnalyzer;
use TomasVotruba\Blog\ValueObject\Post;
use TomasVotruba\FrameworkStats\Exception\ShouldNotHappenException;
use TomasVotruba\Website\ValueObject\Option;

final class PostFactory
{
    /**
     * @var string
     */
    private const SLASHES_WITH_SPACES_REGEX = '(?:---[\s]*[\r\n]+)';

    /**
     * @var string
     */
    private const CONFIG_CONTENT_REGEX = '#^\s*' . self::SLASHES_WITH_SPACES_REGEX . '?(?<config>.*?)' . self::SLASHES_WITH_SPACES_REGEX . '(?<content>.*?)$#s';

    /**
     * @see https://regex101.com/r/9xssch/1
     * @var string
     */
    private const HEADLINE_LEVEL_REGEX = '#<h(?<level>\d+)>(?<headline>.*?)<\/h\d+>#';

    private string $projectDir;

    private string $siteUrl;

    public function __construct(
        private ParsedownExtra $parsedownExtra,
        private PathAnalyzer $pathAnalyzer,
        private RouterInterface $router,
        private TestedPostAnalyzer $testedPostAnalyzer,
        ParameterProvider $parameterProvider,
        private FileSystemGuard $fileSystemGuard
    ) {
        $siteUrl = $parameterProvider->provideStringParameter(Option::SITE_URL);
        $this->siteUrl = rtrim($siteUrl, '/');

        $projectDir = $parameterProvider->provideStringParameter(Option::KERNEL_PROJECT_DIR);
        $this->projectDir = $projectDir;
    }

    public function createFromFileInfo(SmartFileInfo $smartFileInfo): Post
    {
        $matches = Strings::match($smartFileInfo->getContents(), self::CONFIG_CONTENT_REGEX);

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

        $htmlContent = $this->decorateHeadlineWithId($htmlContent);

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

    /**
     * @param array<string, mixed> $configuration
     */
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

    /**
     * Before:
     * <h1>Hey</h1>
     *
     * After:
     * <h1 id="hey">Hey</h1>
     *
     * Then the headline can be anchored in url as "#hey"
     */
    private function decorateHeadlineWithId(string $htmlContent): string
    {
        return Strings::replace($htmlContent, self::HEADLINE_LEVEL_REGEX, function ($matches) {
            $level = $matches['level'];
            $headline = $matches['headline'];
            $idValue = Strings::webalize($headline);

            return sprintf('<h%d id="%s">%s</h%d>', $level, $idValue, $headline, $level);
        });
    }

    private function createAbsoluteUrl(string $slug): string
    {
        $siteUrl = rtrim($this->siteUrl, '/');

        return $siteUrl . $this->router->generate('post_detail', [
            'slug' => $slug,
        ]);
    }
}
