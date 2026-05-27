<?php

declare(strict_types=1);

namespace App\ValueObject;

final readonly class PhpstanRulePackage
{
    public function __construct(
        private string $package,
        private string $group,
        private string $description,
        private string $url,
    ) {
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
