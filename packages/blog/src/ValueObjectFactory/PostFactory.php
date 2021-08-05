<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\ValueObjectFactory;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use ParsedownExtra;
use Stringy\Stringy;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Yaml\Yaml;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;
use TomasVotruba\Blog\Exception\InvalidPostConfigurationException;
use TomasVotruba\Blog\FileSystem\PathAnalyzer;
use TomasVotruba\Blog\PostSnippetDecorator;
use TomasVotruba\Blog\Validation\PostGuard;
use TomasVotruba\Blog\ValueObject\Post;
use TomasVotruba\Website\Exception\ShouldNotHappenException;
use TomasVotruba\Website\ValueObject\Option;
use TomasVotruba\Website\ValueObject\RouteName;

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
        private PostGuard $postGuard,
        private PostSnippetDecorator $postSnippetDecorator,
        ParameterProvider $parameterProvider,
        private FileSystemGuard $fileSystemGuard
    ) {
        $siteUrl = $parameterProvider->provideStringParameter(Option::SITE_URL);
        $this->siteUrl = rtrim($siteUrl, '/');

        $this->projectDir = $parameterProvider->provideStringParameter(Option::KERNEL_PROJECT_DIR);
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

        if (! isset($matches['content'])) {
            throw new InvalidPostConfigurationException('Post content is missing');
        }

        $slug = $this->pathAnalyzer->getSlug($smartFileInfo);
        $htmlContent = $this->parsedownExtra->parse($matches['content']);

        $htmlContent = $this->postSnippetDecorator->decorateHtmlContent($htmlContent);

        $updatedAt = isset($configuration['updated_since']) ? DateTime::from($configuration['updated_since']) : null;
        $deprecatedAt = isset($configuration['deprecated_since']) ? DateTime::from(
            $configuration['deprecated_since']
        ) : null;

        $tweetText = $configuration['tweet'] ?? null;

        $post = new Post(
            $id,
            $title,
            slug: $slug,
            dateTime: $this->pathAnalyzer->resolveDateTime($smartFileInfo),
            perex: $configuration['perex'],
            htmlContent: $this->decorateHeadlineWithId($htmlContent),
            tweetText: $tweetText,
            tweetImage: $this->resolveTweetImage($configuration),
            updatedAt: $updatedAt,
            updatedMessage: $configuration['updated_message'] ?? null,
            sourceRelativePath: $this->getSourceRelativePath($smartFileInfo),
            deprecatedAt: $deprecatedAt,
            deprecatedMessage: $configuration['deprecated_message'] ?? null,
            language: $configuration['lang'] ?? null,
            absoluteUrl: $this->createAbsoluteUrl($slug)
        );

        $this->postGuard->validate($post);

        return $post;
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
        if (\str_starts_with($tweetImage, 'https://')) {
            return $tweetImage;
        }

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
     * Before: <h1>Hey</h1>
     *
     * After: <h1 id="hey">Hey</h1>
     *
     * Then the headline can be anchored in url as "#hey"
     */
    private function decorateHeadlineWithId(string $htmlContent): string
    {
        return Strings::replace($htmlContent, self::HEADLINE_LEVEL_REGEX, function ($matches): string {
            $level = (int) $matches['level'];
            $headline = (string) $matches['headline'];

            $clearHeadline = strip_tags($headline);
            $headlineStringy = new Stringy($clearHeadline);

            $stringy = $headlineStringy->dasherize()
                ->replace("'", '')
                ->toLowerCase();

            return sprintf('<h%d id="%s">%s</h%d>', $level, $stringy, $headline, $level);
        });
    }

    private function createAbsoluteUrl(string $slug): string
    {
        $siteUrl = rtrim($this->siteUrl, '/');

        return $siteUrl . $this->router->generate(RouteName::POST_DETAIL, [
            'slug' => $slug,
        ]);
    }
}
