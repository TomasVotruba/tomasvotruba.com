<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\ValueObject;

use DateTimeInterface;
use Nette\Utils\DateTime;

final class Post
{
    /**
     * @var int
     */
    private const READ_WORDS_PER_MINUTE = 260;

    private string $content;

    private string $plaintextContent;

    public function __construct(
        private int $id,
        private string $title,
        private string $slug,
        private DateTimeInterface $dateTime,
        private string $perex,
        private string $htmlContent,
        private ?string $tweetText,
        private ?string $tweetImage,
        private ?DateTimeInterface $updatedAt,
        private ?string $updatedMessage,
        private bool $isTested,
        private ?string $testSlug,
        private string $sourceRelativePath,
        private ?DateTimeInterface $deprecatedAt,
        private ?string $deprecatedMessage,
        private ?string $language,
        private string $absoluteUrl
    ) {
        $this->plaintextContent = strip_tags($htmlContent);
    }

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

    public function getPerex(): string
    {
        return $this->perex;
    }

    public function getHtmlContent(): string
    {
        return $this->htmlContent;
    }

    public function getTweetText(): ?string
    {
        return $this->tweetText;
    }

    public function hasTweet(): bool
    {
        return $this->tweetText !== null;
    }

    public function getTweetImage(): ?string
    {
        return $this->tweetImage;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    public function getReadingTimeInMinutes(): int
    {
        $wordCount = substr_count($this->plaintextContent, ' ') + 1;

        return (int) ceil($wordCount / self::READ_WORDS_PER_MINUTE);
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function isTested(): bool
    {
        return $this->isTested;
    }

    public function getTestSlug(): ?string
    {
        return $this->testSlug;
    }

    public function getGitHubEditUrl(): string
    {
        return 'https://github.com/tomasvotruba/tomasvotruba.com/edit/master/' . $this->sourceRelativePath;
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

    public function getAbsoluteUrl(): string
    {
        return $this->absoluteUrl;
    }
}
