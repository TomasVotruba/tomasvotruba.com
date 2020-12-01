<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\ValueObject;

final class Cluster
{
    private string $title;

    private string $description;

    /**
     * @var Post[]
     */
    private array $posts;

    /**
     * @param Post[] $posts
     */
    public function __construct(string $title, string $description, array $posts)
    {
        $this->title = $title;
        $this->posts = $posts;
        $this->description = $description;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Post[]
     */
    public function getPosts(): array
    {
        return $this->posts;
    }
}
