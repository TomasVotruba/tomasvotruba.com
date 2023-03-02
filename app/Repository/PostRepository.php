<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Post;
use App\EntityFactory\PostFactory;
use App\Exception\ShouldNotHappenException;
use Webmozart\Assert\Assert;

/**
 * @see \App\Tests\Repository\PostRepositoryTest
 */
final class PostRepository
{
    /**
     * @var Post[]
     */
    private array $posts = [];

    public function __construct(
        private readonly PostFactory $postFactory
    ) {
        $this->posts = $this->loadPostsFromStorage();
    }

    /**
     * @return Post[]
     */
    public function fetchAll(): array
    {
        return $this->posts;
    }

    public function getBySlug(string $slug): Post
    {
        $slug = rtrim($slug, '/');
        foreach ($this->posts as $post) {
            $postSlug = rtrim($post->getSlug(), '/');
            if ($postSlug === $slug) {
                return $post;
            }
        }

        if (isset($this->posts[$slug])) {
            return $this->posts[$slug];
        }

        throw new ShouldNotHappenException(sprintf('Post for slug "%s" was not found.', $slug));
    }

    /**
     * @return Post[]
     */
    public function fetchLast(int $limit): array
    {
        return array_slice($this->fetchAll(), 0, $limit);
    }

    /**
     * @return Post[]
     */
    private function loadPostsFromStorage(): array
    {
        $posts = [];

        $markdownFilePaths = glob(__DIR__ . '/../../resources/posts/*/*.md');
        Assert::allString($markdownFilePaths);

        foreach ($markdownFilePaths as $markdownFilePath) {
            $post = $this->postFactory->createFromFilePath($markdownFilePath);

            if (isset($posts[$post->getId()])) {
                $message = sprintf('Post with id "%d" is duplicated. Increase it to higher one', $post->getId());
                throw new ShouldNotHappenException($message);
            }

            $posts[$post->getId()] = $post;
        }

        return $this->sortByDateTime($posts);
    }

    /**
     * @param Post[] $posts
     * @return Post[]
     */
    private function sortByDateTime(array $posts): array
    {
        uasort(
            $posts,
            static fn (Post $firstPost, Post $secondPost): int => $secondPost->getDateTime() <=> $firstPost->getDateTime()
        );

        return $posts;
    }
}
