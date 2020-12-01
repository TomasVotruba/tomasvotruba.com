<?php

declare(strict_types=1);

namespace TomasVotruba\CleaningLady\ValueObject;

use Nette\Utils\Strings;

final class CleaningSection
{
    /**
     * @param CleaningItem[] $cleaningItems
     */
    public function __construct(private string $headline, private array $cleaningItems)
    {
    }

    public function getHeadline(): string
    {
        return $this->headline;
    }

    public function getHeadlineAnchor(): string
    {
        return Strings::webalize($this->headline);
    }

    /**
     * @return CleaningItem[]
     */
    public function getCleaningItems(): array
    {
        return $this->cleaningItems;
    }
}
