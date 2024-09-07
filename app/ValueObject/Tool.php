<?php

declare(strict_types=1);

namespace App\ValueObject;

use Webmozart\Assert\Assert;

final readonly class Tool
{
    /**
     * @param array<string, string> $tryCommands
     */
    public function __construct(
        private string $name,
        private string $when,
        private string $why,
        private string $link,
        private string $post,
        private string $composer,
        private array $tryCommands = [],
        private bool $isPhpstanExtension = false,
    ) {
        Assert::allString(array_keys($tryCommands));
        Assert::allString($tryCommands);
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

    /**
     * @return array<string, string>
     */
    public function getTryCommands(): array
    {
        return $this->tryCommands;
    }

    public function isPhpstanExtension(): bool
    {
        return $this->isPhpstanExtension;
    }
}
