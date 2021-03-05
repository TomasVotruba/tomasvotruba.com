<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Blog\Templating\ResponseRenderer;
use TomasVotruba\Website\ValueObject\RouteName;

final class PostController
{
    public function __construct(
        private PostRepository $postRepository,
        private ResponseRenderer $responseRenderer
    ) {
    }

    #[Route(path: '/blog/{slug}', name: RouteName::POST_DETAIL, requirements: [
        'slug' => '(\d+\/\d+.+|[\w\-]+)',
    ])]
    public function __invoke(string $slug): Response
    {
        $post = $this->postRepository->getBySlug($slug);

        return $this->responseRenderer->render('blog/post_detail.twig', [
            'post' => $post,
            'title' => $post->getTitle(),
        ]);
    }
}
