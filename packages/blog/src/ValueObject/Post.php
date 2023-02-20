<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\ValueObject;

use DateTimeInterface;
use Nette\Utils\DateTime;

/**
 * @api Getter methods are used in twig
 */
final class Post
{
    /**
     * Seems like unused property, but used in twig actually with dot syntax.
     */
    private readonly string $content;

    public function __construct(
        private readonly int $id,
        private readonly string $title,
        private readonly string $slug,
        private readonly DateTimeInterface $dateTime,
        private readonly string $perex,
        private readonly string $htmlContent,
        private readonly ?DateTimeInterface $updatedAt,
        private readonly ?string $updatedMessage,
        private readonly ?DateTimeInterface $deprecatedAt,
        private readonly ?string $deprecatedMessage,
        private readonly ?string $language,
        private readonly ?int $nextPostId
    ) {
    }

    /**
     * @api
     */
    public function getContent(): string
    {
        return $this->content;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getClearTitle(): string
    {
        return str_replace('&nbsp;', ' ', $this->title);
    }

    public function getPerex(): string
    {
        return $this->perex;
    }

    public function getHtmlContent(): string
    {
        return $this->htmlContent;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getDeprecatedAt(): ?DateTimeInterface
    {
        return $this->deprecatedAt;
    }

    public function isDeprecated(): bool
    {
        return $this->deprecatedAt !== null;
    }

    public function isUpdated(): bool
    {
        return $this->updatedAt !== null;
    }

    public function getDeprecatedMessage(): ?string
    {
        return $this->deprecatedMessage;
    }

    public function getUpdatedMessage(): ?string
    {
        return $this->updatedMessage;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getYear(): int
    {
        return (int) $this->dateTime->format('Y');
    }

    public function isFuture(): bool
    {
        return $this->dateTime > DateTime::from('now');
    }

    public function getNextPostId(): int|null
    {
        return $this->nextPostId;
    }
}
