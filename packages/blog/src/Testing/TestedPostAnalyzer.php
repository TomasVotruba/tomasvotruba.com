<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Testing;

use TomasVotruba\Blog\Tests\Contract\PostTestInterface;

final class TestedPostAnalyzer
{
    /**
     * @param PostTestInterface[] $postTests
     */
    public function __construct(private array $postTests)
    {
    }

    public function isPostIdTested(int $id): bool
    {
        foreach ($this->postTests as $postTest) {
            if ($id !== $postTest->getPostId()) {
                continue;
            }

            return true;
        }

        return false;
    }
}
