<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Blog\Templating\ResponseRenderer;
use TomasVotruba\Website\ValueObject\RouteName;

final class ClustersController
{
    public function __construct(
        private PostRepository $postRepository,
        private ResponseRenderer $responseRenderer
    ) {
    }

    #[Route(path: 'clusters', name: RouteName::CLUSTERS)]
    public function __invoke(): Response
    {
        return $this->responseRenderer->render('blog/clusters.twig', [
            'title' => 'Clusters',
            'posts' => $this->postRepository->fetchAllEnglishNonDeprecated(),
        ]);
    }
}
