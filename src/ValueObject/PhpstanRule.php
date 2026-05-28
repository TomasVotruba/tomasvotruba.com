<?php

declare(strict_types=1);

namespace App\ValueObject;

final readonly class PhpstanRule
{
    public function __construct(
        private string $group,
        private string $package,
        private string $class,
        private string $name,
        private string $message,
        private string $description,
        private string $nodeType,
        private string $wrongCode,
        private string $correctCode,
        private string $identifier = '',
        private string $tip = '',
    ) {
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getNodeType(): string
    {
        return $this->nodeType;
    }

    public function getWrongCode(): string
    {
        return $this->wrongCode;
    }

    public function getCorrectCode(): string
    {
        return $this->correctCode;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getTip(): string
    {
        return $this->tip;
    }

    /**
     * Keywords derived from the rule name + group (CamelCase split, lowercased, de-duped).
     * Used as extra signal for the search index.
     *
     * @return list<string>
     */
    public function getTags(): array
    {
        $source = preg_replace('/Rule$/', '', $this->name) . ' ' . $this->group;
        $spaced = preg_replace('/(?<=[a-z0-9])(?=[A-Z])/', ' ', $source) ?? $source;
        $words = preg_split('/[^A-Za-z0-9]+/', strtolower($spaced)) ?: [];

        $stopWords = ['rule', 'the', 'and', 'for', 'with', 'that', 'this', 'php', 'phpstan'];

        $tags = [];
        foreach ($words as $word) {
            if (strlen($word) < 2) {
                continue;
            }
            if (in_array($word, $stopWords, true)) {
                continue;
            }
            $tags[$word] = true;
        }

        return array_keys($tags);
    }

    public function getSlug(): string
    {
        return str($this->class)->replace('\\', '-')->slug()->toString();
    }
}
