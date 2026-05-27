<?php

declare(strict_types=1);

namespace App\ValueObject;

final readonly class PhpstanRule
{
    public function __construct(
        private string $group,
        private string $package,
        private string $class,
        private string $name,
        private string $message,
        private string $description,
        private string $nodeType,
        private string $wrongCode,
        private string $correctCode,
    ) {
    }

    public function getGroup(): string
    {
        return $this->group;
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

    public function getNodeType(): string
    {
        return $this->nodeType;
    }

    public function getWrongCode(): string
    {
        return $this->wrongCode;
    }

    public function getCorrectCode(): string
    {
        return $this->correctCode;
    }

    public function getSlug(): string
    {
        return str($this->class)->replace('\\', '-')->slug()->toString();
    }
}
