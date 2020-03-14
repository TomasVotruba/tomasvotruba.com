<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Repository;

use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;
use TomasVotruba\Blog\ValueObject\Post;
use TomasVotruba\Blog\ValueObjectFactory\PostFactory;
use TomasVotruba\FrameworkStats\Exception\ShouldNotHappenException;

final class PostRepository
{
    /**
     * @var string
     */
    private const POST_DIRECTORY = __DIR__ . '/../../config/data';

    /**
     * @var Post[]
     */
    private $posts = [];

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    /**
     * @var PostFactory
     */
    private $postFactory;

    public function __construct(FinderSanitizer $finderSanitizer, PostFactory $postFactory)
    {
        $this->finderSanitizer = $finderSanitizer;
        $this->postFactory = $postFactory;
    }

    /**
     * @return Post[]
     */
    public function fetchAll(): array
    {
        $posts = [];
        foreach ($this->findPostMarkdownFileInfos() as $smartFileInfo) {
            $post = $this->postFactory->createFromFileInfo($smartFileInfo);
            $posts[$post->getId()] = $post;
        }

        uasort($posts, function (Post $firstPost, Post $secondPost) {
            return $secondPost->getDateTime() <=> $firstPost->getDateTime();
        });

        return $posts;
    }

    /**
     * @return Post[]
     */
    public function fetchAllEnglishNonDeprecated(): array
    {
        if ($this->posts !== []) {
            return $this->posts;
        }

        foreach ($this->findPostMarkdownFileInfos() as $smartFileInfo) {
            $post = $this->postFactory->createFromFileInfo($smartFileInfo);
            $this->posts[$post->getId()] = $post;
        }

        // keep only English posts
        $this->posts = array_filter($this->posts, function (Post $post) {
            return $post->getLanguage() === null;
        });

        // keep only active posts
        $this->posts = array_filter($this->posts, function (Post $post) {
            return ! $post->isDeprecated();
        });

        uasort($this->posts, function (Post $firstPost, Post $secondPost) {
            return $secondPost->getDateTime() <=> $firstPost->getDateTime();
        });

        return $this->posts;
    }

    /**
     * @return Post[]
     */
    public function fetchByYear(int $year): array
    {
        return array_filter($this->fetchAllEnglishNonDeprecated(), function (Post $post) use ($year) {
            return $post->getYear() === $year;
        });
    }

    public function get(int $id): Post
    {
        foreach ($this->fetchAll() as $post) {
            if ($post->getId() !== $id) {
                continue;
            }

            return $post;
        }

        throw new ShouldNotHappenException();
    }

    public function getBySlug(string $slug): Post
    {
        foreach ($this->fetchAll() as $post) {
            if ($post->getSlug() !== $slug) {
                continue;
            }

            return $post;
        }

        throw new ShouldNotHappenException();
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findPostMarkdownFileInfos(): array
    {
        $finder = new Finder();
        $finder->files()
            ->in(self::POST_DIRECTORY)
            ->name('*.md');

        return $this->finderSanitizer->sanitize($finder);
    }
}
