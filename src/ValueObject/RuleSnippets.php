<?php

declare(strict_types=1);

namespace App\ValueObject;

final readonly class RuleSnippets
{
    public function __construct(
        private string $description,
        private string $wrongCode,
        private string $correctCode,
    ) {
    }

    public static function empty(): self
    {
        return new self('', '', '');
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getWrongCode(): string
    {
        return $this->wrongCode;
    }

    public function getCorrectCode(): string
    {
        return $this->correctCode;
    }
}
