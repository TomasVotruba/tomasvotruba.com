<?php

declare(strict_types=1);

namespace TomasVotruba\Website\SymfonyStaticDumper\ControllerWithDataProvider;

use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use TomasVotruba\Website\Controller\BookDetailController;
use TomasVotruba\Website\Repository\BookRepository;

final class BookControllerWithDataProvider implements ControllerWithDataProviderInterface
{
    public function __construct(
        private readonly BookRepository $bookRepository
    ) {
    }

    public function getControllerClass(): string
    {
        return BookDetailController::class;
    }

    public function getControllerMethod(): string
    {
        return '__invoke';
    }

    /**
     * @return string[]
     */
    public function getArguments(): array
    {
        $slugs = [];

        foreach ($this->bookRepository->fetchAll() as $book) {
            $slugs[] = $book->getSlug();
        }

        return $slugs;
    }
}
