<?php

declare(strict_types=1);

namespace TomasVotruba\Website\EntityFactory;

use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Yaml\Yaml;
use TomasVotruba\Website\Entity\Post;
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
            throw new ShouldNotHappenException('Post content is missing');
        }

        $slug = $this->pathAnalyzer->getSlug($filePath);
        $updatedAt = isset($configuration['updated_since']) ? DateTime::from($configuration['updated_since']) : null;

        $post = new Post(
            $id,
            $title,
            $slug,
            $this->pathAnalyzer->resolveDateTime($filePath),
            $configuration['perex'],
            $matches['content'],
            $updatedAt,
            $configuration['updated_message'] ?? null,
        );

        $this->postGuard->validate($post);

        return $post;
    }
}
