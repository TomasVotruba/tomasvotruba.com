<?php

declare(strict_types=1);

namespace TomasVotruba\CleaningLady\ValueObject;

final class CleaningChecklist
{
    /**
     * @var CleaningSection[]
     */
    private array $cleaningSections = [];

    /**
     * @param CleaningSection[] $cleaningSections
     */
    public function __construct(array $cleaningSections)
    {
        $this->cleaningSections = $cleaningSections;
    }

    /**
     * @return CleaningSection[]
     */
    public function getCleaningSections(): array
    {
        return $this->cleaningSections;
    }
}
