<?php

declare(strict_types=1);

namespace TomasVotruba\Website\ValueObject;

final class Cluster
{
    private string $name;

    /**
     * @var int[]
     */
    private array $postIds;
    private string $description;

    /**
     * @param int[] $postIds
     */
    public function __construct(string $name, array $postIds, string $description)
    {
        $this->name = $name;
        $this->postIds = $postIds;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPostIds(): array
    {
        return $this->postIds;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
