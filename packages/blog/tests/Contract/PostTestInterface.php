<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Tests\Contract;

interface PostTestInterface
{
    public function getPostId(): int;
}
