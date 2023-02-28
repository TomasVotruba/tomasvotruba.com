<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Validation;

use DateTimeInterface;
use TomasVotruba\Website\Exception\InvalidPostConfigurationException;
use TomasVotruba\Website\Entity\Post;

final class PostGuard
{
    public function validate(Post $post): void
    {
        $this->ensureDeprecatedHasMessage($post);
        $this->ensureUpdatedHasMessage($post);
    }

    private function ensureDeprecatedHasMessage(Post $post): void
    {
        if (! $post->getDeprecatedAt() instanceof DateTimeInterface) {
            return;
        }

        $deprecatedMessage = $post->getDeprecatedMessage();
        if ($deprecatedMessage) {
            return;
        }

        $message = sprintf('"deprecated_message" is missing in post %d', $post->getId());
        throw new InvalidPostConfigurationException($message);
    }

    private function ensureUpdatedHasMessage(Post $post): void
    {
        if (! $post->getUpdatedAt() instanceof DateTimeInterface) {
            return;
        }

        $updatedMessage = $post->getUpdatedMessage();
        if ($updatedMessage) {
            return;
        }

        $message = sprintf('"updated_message" is missing in post %d', $post->getId());
        throw new InvalidPostConfigurationException($message);
    }
}
