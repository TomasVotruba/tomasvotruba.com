<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\ValueObjectFactory;

use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use ParsedownExtra;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Yaml\Yaml;
use TomasVotruba\Blog\Exception\InvalidPostConfigurationException;
use TomasVotruba\Blog\FileSystem\PathAnalyzer;
use TomasVotruba\Blog\Validation\PostGuard;
use TomasVotruba\Blog\ValueObject\Post;
use TomasVotruba\Website\Exception\ShouldNotHappenException;

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

    public function __construct(
        private readonly ParsedownExtra $parsedownExtra,
        private readonly PathAnalyzer $pathAnalyzer,
        private readonly PostGuard $postGuard,
    ) {
    }

    public function createFromFilePath(string $filePath): Post
    {
        $fileContents = FileSystem::read($filePath);

        $matches = Strings::match($fileContents, self::CONFIG_CONTENT_REGEX);

        if (! isset($matches['config'])) {
            throw new ShouldNotHappenException();
        }

        $configuration = Yaml::parse($matches['config']);

        $id = $configuration['id'];
        $title = $configuration['title'];

        if (! isset($matches['content'])) {
            throw new InvalidPostConfigurationException('Post content is missing');
        }

        $slug = $this->pathAnalyzer->getSlug($filePath);
        $htmlContent = $this->parsedownExtra->parse($matches['content']);

        $updatedAt = isset($configuration['updated_since']) ? DateTime::from($configuration['updated_since']) : null;
        $deprecatedAt = isset($configuration['deprecated_since']) ? DateTime::from(
            $configuration['deprecated_since']
        ) : null;

        $post = new Post(
            $id,
            $title,
            $slug,
            $this->pathAnalyzer->resolveDateTime($filePath),
            $configuration['perex'],
            $this->decorateHeadlineWithId($htmlContent),
            $updatedAt,
            $configuration['updated_message'] ?? null,
            $deprecatedAt,
            $configuration['deprecated_message'] ?? null,
            $configuration['lang'] ?? null,
            $configuration['next_post_id'] ?? null,
        );

        $this->postGuard->validate($post);

        return $post;
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
        return Strings::replace($htmlContent, self::HEADLINE_LEVEL_REGEX, static function (array $matches): string {
            $level = (int) $matches['level'];
            $headline = (string) $matches['headline'];
            $clearHeadline = strip_tags($headline);
            $asciiSlugger = new AsciiSlugger('en');
            $unicodeString = $asciiSlugger->slug($clearHeadline);
            return sprintf('<h%d id="%s">%s</h%d>', $level, $unicodeString, $headline, $level);
        });
    }
}
