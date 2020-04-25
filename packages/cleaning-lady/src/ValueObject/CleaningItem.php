<?php

declare(strict_types=1);

namespace TomasVotruba\CleaningLady\ValueObject;

final class CleaningItem
{
    private string $description;

    private ?string $helpLink;
    private ?string $whyLink;

    public function __construct(string $description, ?string $helpLink = null, ?string $whyLink = null)
    {
        $this->description = $description;
        $this->helpLink = $helpLink;
        $this->whyLink = $whyLink;
    }

    public function getId(): string
    {
        return md5($this->description);
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getHelpLink(): ?string
    {
        return $this->helpLink;
    }

    public function getWhyLink(): ?string
    {
        return $this->whyLink;
    }
}
