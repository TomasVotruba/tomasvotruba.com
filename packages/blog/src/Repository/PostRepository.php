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

    private FinderSanitizer $finderSanitizer;

    private PostFactory $postFactory;

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

        return $this->sortByDateTime($posts);
    }

    /**
     * @return Post[]
     */
    public function fetchForRss(): array
    {
        $posts = [];
        foreach ($this->findPostMarkdownFileInfos() as $smartFileInfo) {
            $post = $this->postFactory->createFromFileInfo($smartFileInfo);
            $posts[$post->getId()] = $post;
        }

        $posts = $this->filterOutNonEnglish($posts);
        $posts = $this->filterOutDeprecated($posts);
        $posts = $this->filterOutFuture($posts);

        return $this->sortByDateTime($posts);
    }

    /**
     * @return Post[]
     */
    public function fetchAllEnglishNonDeprecated(): array
    {
        $posts = [];

        foreach ($this->findPostMarkdownFileInfos() as $smartFileInfo) {
            $post = $this->postFactory->createFromFileInfo($smartFileInfo);
            $posts[$post->getId()] = $post;
        }

        $posts = $this->filterOutNonEnglish($posts);
        $posts = $this->filterOutDeprecated($posts);

        return $this->sortByDateTime($posts);
    }

    /**
     * @return Post[]
     */
    public function fetchByYear(int $year): array
    {
        return array_filter($this->fetchAllEnglishNonDeprecated(), fn (Post $post) => $post->getYear() === $year);
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

        throw new ShouldNotHappenException(sprintf('Post for slug "%s" was not found.', $slug));
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findPostMarkdownFileInfos(): array
    {
        $finder = new Finder();
        $finder->files()
            ->in(self:: POST_DIRECTORY)
            ->name('*.md');

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @param Post[] $posts
     * @return Post[]
     */
    private function sortByDateTime(array $posts): array
    {
        uasort(
            $posts,
            fn (Post $firstPost, Post $secondPost) => $secondPost->getDateTime() <=> $firstPost->getDateTime()
        );

        return $posts;
    }

    /**
     * @param Post[] $posts
     * @return Post[]
     */
    private function filterOutNonEnglish(array $posts): array
    {
        return array_filter($posts, fn (Post $post) => $post->getLanguage() === null);
    }

    /**
     * @param Post[] $posts
     * @return Post[]
     */
    private function filterOutDeprecated(array $posts): array
    {
        return array_filter($posts, fn (Post $post) => ! $post->isDeprecated());
    }

    /**
     * @param Post[] $posts
     * @return Post[]
     */
    private function filterOutFuture(array $posts): array
    {
        return array_filter($posts, fn (Post $post) => ! $post->isFuture());
    }
}
