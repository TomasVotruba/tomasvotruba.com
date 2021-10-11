<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Repository;

use TomasVotruba\Blog\DataProvider\PostDataProvider;
use TomasVotruba\Blog\ValueObject\Post;
use TomasVotruba\Website\Exception\ShouldNotHappenException;

final class PostRepository
{
    /**
     * @var Post[]
     */
    private array $posts = [];

    public function __construct(PostDataProvider $postDataProvider)
    {
        $this->posts = $postDataProvider->provide();
    }

    /**
     * @return Post[]
     */
    public function getPosts(): array
    {
        return $this->posts;
    }

    /**
     * @return Post[]
     */
    public function fetchForRss(): array
    {
        $posts = $this->posts;

        $posts = $this->filterOutNonEnglish($posts);
        return $this->filterOutFuture($posts);
    }

    /**
     * @return Post[]
     */
    public function fetchAllEnglish(): array
    {
        $posts = $this->posts;
        return $this->filterOutNonEnglish($posts);
    }

    /**
     * @param int[] $ids
     * @return Post[]
     */
    public function findByIds(array $ids): array
    {
        $posts = [];

        foreach ($ids as $id) {
            $posts[] = $this->get($id);
        }

        return $posts;
    }

    public function get(int $id): Post
    {
        foreach ($this->getPosts() as $post) {
            if ($post->getId() !== $id) {
                continue;
            }

            return $post;
        }

        $message = sprintf('Post with id "%d" was not found', $id);
        throw new ShouldNotHappenException($message);
    }

    public function getBySlug(string $slug): Post
    {
        foreach ($this->posts as $post) {
            if ($post->getSlug() === $slug) {
                return $post;
            }
        }

        // extra dash
        $slughWithoutDash = rtrim($slug, '/');
        if (isset($this->posts[$slughWithoutDash])) {
            return $this->posts[$slughWithoutDash];
        }

        throw new ShouldNotHappenException(sprintf('Post for slug "%s" was not found.', $slug));
    }

    public function findPreviousPost(Post $currentPost): Post|null
    {
        $posts = $this->fetchAllEnglish();

        foreach ($posts as $post) {
            if ($post->getId() >= $currentPost->getId()) {
                continue;
            }

            return $post;
        }

        return null;
    }

    /**
     * @param Post[] $posts
     * @return Post[]
     */
    private function filterOutNonEnglish(array $posts): array
    {
        return array_filter($posts, fn (Post $post): bool => $post->getLanguage() === null);
    }

    /**
     * @param Post[] $posts
     * @return Post[]
     */
    private function filterOutFuture(array $posts): array
    {
        return array_filter($posts, fn (Post $post): bool => ! $post->isFuture());
    }
}
