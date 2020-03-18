<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Testing;

use TomasVotruba\Blog\Tests\Contract\PostTestInterface;

final class TestedPostAnalyzer
{
    /**
     * @var PostTestInterface[]
     */
    private array $postTests = [];

    /**
     * @param PostTestInterface[] $postTests
     */
    public function __construct(array $postTests)
    {
        $this->postTests = $postTests;
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
