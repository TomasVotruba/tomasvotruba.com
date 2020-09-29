<?php

declare(strict_types=1);

namespace TomasVotruba\Website\ValueObject;

use Nette\Utils\Strings;

final class PackagistPackage
{
    private string $name;

    private string $shortName;

    private string $description;

    public function __construct(string $name, string $description)
    {
        $this->name = $name;
        $this->shortName = (string) Strings::after($name, '//');
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
