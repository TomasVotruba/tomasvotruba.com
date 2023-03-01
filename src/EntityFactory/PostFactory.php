<?php

declare(strict_types=1);

namespace TomasVotruba\Website\EntityFactory;

use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use ParsedownExtra;
use Symfony\Component\Yaml\Yaml;
use TomasVotruba\Website\Entity\Post;
use TomasVotruba\Website\Exception\InvalidPostConfigurationException;
use TomasVotruba\Website\Exception\ShouldNotHappenException;
use TomasVotruba\Website\FileSystem\PathAnalyzer;
use TomasVotruba\Website\Validation\PostGuard;

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

        $post = new Post(
            $id,
            $title,
            $slug,
            $this->pathAnalyzer->resolveDateTime($filePath),
            $configuration['perex'],
            $htmlContent,
            $updatedAt,
            $configuration['updated_message'] ?? null,
            $configuration['lang'] ?? null,
            $configuration['next_post_id'] ?? null,
        );

        $this->postGuard->validate($post);

        return $post;
    }
}
