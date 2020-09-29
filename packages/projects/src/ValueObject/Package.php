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

    private string $repositoryUrl;

    private int $githubStartsCount;

    private int $totalDownloads;

    public function __construct(
        string $name,
        string $description,
        string $repositoryUrl,
        int $githubStartsCount,
        int $totalDownloads
    ) {
        $this->name = $name;
        $this->resolveShortName($name);

        $this->description = $description;
        $this->repositoryUrl = $repositoryUrl;
        $this->githubStartsCount = $githubStartsCount;
        $this->totalDownloads = $totalDownloads;
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

    public function getRepositoryUrl(): string
    {
        return $this->repositoryUrl;
    }

    public function getGithubStartsCount(): int
    {
        return $this->githubStartsCount;
    }

    public function getTotalDownloads(): int
    {
        return $this->totalDownloads;
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
}
