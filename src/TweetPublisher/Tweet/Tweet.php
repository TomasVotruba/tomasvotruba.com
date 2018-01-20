<?php declare(strict_types=1);

namespace TomasVotruba\Website\TweetPublisher\Tweet;

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

    public function __construct(string $text, ?string $image)
    {
        $this->text = $text;
        $this->image = $image;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
}
