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

    /**
     * @var string
     */
    private $content;

    /**
     * @var string|null
     */
    private $tweetText;

    /**
     * @var string|null
     */
    private $updatedMessage;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $perex;

    /**
     * @var string
     */
    private $htmlContent;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $plaintextContent;

    /**
     * @var string|null
     */
    private $tweetImage;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var bool
     */
    private $isTested = false;

    /**
     * @var string|null
     */
    private $testSlug;

    /**
     * @var string
     */
    private $sourceRelativePath;

    /**
     * @var string|null
     */
    private $deprecatedMessage;

    /**
     * @var string|null
     */
    private $language;

    /**
     * @var DateTimeInterface
     */
    private $dateTime;

    /**
     * @var DateTimeInterface|null
     */
    private $updatedAt;

    /**
     * @var DateTimeInterface|null
     */
    private $deprecatedAt;

    public function __construct(
        int $id,
        string $title,
        string $slug,
        DateTimeInterface $dateTime,
        string $perex,
        string $htmlContent,
        ?string $tweetText,
        ?string $tweetImage,
        ?DateTimeInterface $updatedAt,
        ?string $updatedMessage,
        bool $isTested,
        ?string $testSlug,
        string $sourceRelativePath,
        ?DateTimeInterface $deprecatedAt,
        ?string $deprecatedMessage,
        ?string $language
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->perex = $perex;
        $this->htmlContent = $htmlContent;
        $this->plaintextContent = strip_tags($htmlContent);
        $this->tweetText = $tweetText;
        $this->tweetImage = $tweetImage;
        $this->slug = $slug;
        $this->dateTime = $dateTime;
        $this->updatedAt = $updatedAt;
        $this->isTested = $isTested;
        $this->testSlug = $testSlug;
        $this->sourceRelativePath = $sourceRelativePath;
        $this->deprecatedAt = $deprecatedAt;
        $this->deprecatedMessage = $deprecatedMessage;
        $this->updatedMessage = $updatedMessage;
        $this->language = $language;
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

    public function getPlaintextContent(): string
    {
        return $this->plaintextContent;
    }

    public function getTweetText(): ?string
    {
        return $this->tweetText;
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
}
