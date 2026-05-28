<?php

declare(strict_types=1);

namespace App\ValueObject;

final readonly class ParsedRuleClass
{
    public function __construct(
        private string $shortName,
        private string $fullyQualifiedName,
        private string $message,
        private string $nodeType,
        private string $identifier,
        private string $tip,
        private string $docComment,
    ) {
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getFullyQualifiedName(): string
    {
        return $this->fullyQualifiedName;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getNodeType(): string
    {
        return $this->nodeType;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getTip(): string
    {
        return $this->tip;
    }

    public function getDocComment(): string
    {
        return $this->docComment;
    }
}
