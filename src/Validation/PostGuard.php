<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Validation;

use DateTimeInterface;
use TomasVotruba\Website\Entity\Post;
use TomasVotruba\Website\Exception\ShouldNotHappenException;

final class PostGuard
{
    public function validate(Post $post): void
    {
        if (! $post->getUpdatedAt() instanceof DateTimeInterface) {
            return;
        }

        $updatedMessage = $post->getUpdatedMessage();
        if ($updatedMessage) {
            return;
        }

        $message = sprintf('"updated_message" is missing in post %d', $post->getId());
        throw new ShouldNotHappenException($message);
    }
}
