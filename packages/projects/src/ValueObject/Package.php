<?php

declare(strict_types=1);

namespace TomasVotruba\Projects\ValueObject;

use Nette\Utils\Strings;
use TomasVotruba\Website\Exception\ShouldNotHappenException;

final class Package
{
    private string $name;

    private string $shortName;

    private string $description;

    private string $githubUrl;

    public function __construct(string $name, string $description, string $githubUrl)
    {
        $this->name = $name;
        $this->resolveShortName($name);

        $this->description = $description;
        $this->resolveGithubUrl($githubUrl);
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

    public function getGithubUrl(): string
    {
        return $this->githubUrl;
    }

    private function resolveShortName(string $name): void
    {
        $shortName = (string) Strings::after($name, '/');
        if ($shortName === '') {
            $message = sprintf('Short name could not be determined from "%s"', $name);
            throw new ShouldNotHappenException($message);
        }

        $this->shortName = $shortName;
    }

    private function resolveGithubUrl(string $githubUrl): void
    {
        $shortName = (string) Strings::before($githubUrl, '.git');
        if ($shortName === '') {
            $message = sprintf('Github url could not be determined from "%s"', $githubUrl);
            throw new ShouldNotHappenException($message);
        }

        $this->githubUrl = $githubUrl;
    }
}
