<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher\Tweet;

use Nette\Utils\Strings;

final class Tweet
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var null|string
     */
    private $image;

    private function __construct(string $text, ?string $image = null)
    {
        $this->text = $text;
        $this->image = $image;
    }

    public static function createFromText(string $text): self
    {
        return new self($text);
    }

    public static function createFromTextAndImage(string $text, ?string $image): self
    {
        return new self($text, $image);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function isSimilarTo(self $anotherTweet): bool
    {
        return Strings::startsWith(
            $this->text,
            // published tweet is usually modified by Twitter API, so we just use starting part of it
            substr($anotherTweet->getText(), 0, 50)
        );
    }
}
