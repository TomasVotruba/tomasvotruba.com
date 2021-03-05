<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Blog\Templating\ResponseRenderer;
use TomasVotruba\Website\ValueObject\RouteName;

final class BlogArchiveController
{
    public function __construct(
        private PostRepository $postRepository,
        private ResponseRenderer $responseRenderer
    ) {
    }

    #[Route(path: '/archive', name: RouteName::BLOG_ARCHIVE)]
    public function __invoke(): Response
    {
        return $this->responseRenderer->render('blog/archive.twig', [
            'title' => 'Post Archive',
            'posts_by_year' => $this->postRepository->groupByYear(),
        ]);
    }
}
