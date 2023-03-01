<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Repository;

use TomasVotruba\Website\DataProvider\PostDataProvider;
use TomasVotruba\Website\Entity\Post;
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

    ///**
    // * @return Post[]
    // */
    //public function getPosts(): array
    //{
    //    return $this->posts;
    //}

    /**
     * @return Post[]
     */
    public function fetchForRss(): array
    {
        return $this->filterOutFuture($this->posts);
    }

    /**
     * @return Post[]
     */
    public function fetchAll(): array
    {
        return $this->posts;
    }

    //public function get(int $id): Post
    //{
    //    foreach ($this->getPosts() as $post) {
    //        if ($post->getId() !== $id) {
    //            continue;
    //        }
    //
    //        return $post;
    //    }
    //
    //    $message = sprintf('Post with id "%d" was not found', $id);
    //    throw new ShouldNotHappenException($message);
    //}

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
     * @param Post[] $posts
     * @return Post[]
     */
    private function filterOutFuture(array $posts): array
    {
        return array_filter($posts, static fn (Post $post): bool => ! $post->isFuture());
    }
}
