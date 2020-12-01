<?php

declare(strict_types=1);

namespace TomasVotruba\CleaningLady\ValueObject;

final class CleaningChecklist
{
    /**
     * @param CleaningSection[] $cleaningSections
     */
    public function __construct(private array $cleaningSections)
    {
    }

    /**
     * @return CleaningSection[]
     */
    public function getCleaningSections(): array
    {
        return $this->cleaningSections;
    }
}
