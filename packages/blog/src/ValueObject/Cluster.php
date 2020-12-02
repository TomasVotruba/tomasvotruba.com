<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\ValueObject;

use Nette\Utils\Strings;

final class Cluster
{
    private string $slug;

    public function __construct(
        private string $title,
        private string $description,
        /**
         * @var Post[]
         */
        private array $posts = []
    ) {
        $this->slug = Strings::webalize($title);
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getPostCount(): int
    {
        return count($this->posts);
    }
}
