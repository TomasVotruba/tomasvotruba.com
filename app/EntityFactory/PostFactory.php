<?php

declare(strict_types=1);

namespace App\EntityFactory;

use App\Entity\Post;
use App\Exception\ShouldNotHappenException;
use App\FileSystem\PathAnalyzer;
use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Yaml\Yaml;

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

        if (! isset($configuration['title'])) {
            $errorMessage = sprintf('"title" is missing in post: %d', $id);
            throw new ShouldNotHappenException($errorMessage);
        }

        $title = $configuration['title'];

        if (! isset($matches['content'])) {
            throw new ShouldNotHappenException('Post content is missing');
        }

        $slug = $this->pathAnalyzer->getSlug($filePath);
        $updatedAt = isset($configuration['updated_since']) ? DateTime::from($configuration['updated_since']) : null;

        return new Post(
            $id,
            $title,
            $slug,
            $this->pathAnalyzer->resolveDateTime($filePath),
            $configuration['perex'],
            $matches['content'],
            $updatedAt,
            $configuration['updated_message'] ?? null,
        );
    }
}
