<?php

declare(strict_types=1);

namespace TomasVotruba\CleaningLady\ValueObject;

final class CleaningItem
{
    public function __construct(
        private string $description,
        private ?string $helpLink = null,
        private ?string $whyLink = null
    ) {
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
