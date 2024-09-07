<?php

declare(strict_types=1);

namespace App\ValueObject;

final class Tool
{
    public function __construct(
        private string $name,
        private string $when,
        private string $why,
        private string $link,
        private string $post,
        private string $composer,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWhen(): string
    {
        return $this->when;
    }

    public function getWhy(): string
    {
        return $this->why;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getPost(): ?string
    {
        return $this->post;
    }

    public function getComposer(): ?string
    {
        return $this->composer;
    }
}
