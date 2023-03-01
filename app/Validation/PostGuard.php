<?php

declare(strict_types=1);

namespace App\Validation;

use App\Entity\Post;
use App\Exception\ShouldNotHappenException;
use DateTimeInterface;

final class PostGuard
{
    public function ensureUpdatedMessageIsPresent(Post $post): void
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
