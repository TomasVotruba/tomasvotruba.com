<?php

declare(strict_types=1);

namespace App\Repository;

use App\DataProvider\PostDataProvider;
use App\Entity\Post;
use App\Exception\ShouldNotHappenException;

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
}
