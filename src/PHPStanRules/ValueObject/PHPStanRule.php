<?php

declare(strict_types=1);

namespace App\PHPStanRules\ValueObject;

final readonly class PHPStanRule
{
    public function __construct(
        private string $package,
        private string $class,
        private string $name,
        private string $message,
        private string $description,
        private string $wrongCode,
        private string $correctCode,
        private string $tip = '',
    ) {
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMessage(): string
    {
        return $this->message;
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

    public function getTip(): string
    {
        return $this->tip;
    }

    public function getSlug(): string
    {
        return str($this->class)->replace('\\', '-')
            ->slug()
            ->toString();
    }
}
