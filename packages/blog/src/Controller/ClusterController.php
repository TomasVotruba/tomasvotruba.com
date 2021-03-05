<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\ClusterRepository;
use TomasVotruba\Blog\Templating\ResponseRenderer;
use TomasVotruba\Website\ValueObject\RouteName;

final class ClusterController
{
    public function __construct(
        private ClusterRepository $clusterRepository,
        private ResponseRenderer $responseRenderer
    ) {
    }

    #[Route(path: '/cluster/{slug}', name: RouteName::CLUSTER_DETAIL, requirements: [
        'slug' => '[\w\-]+',
    ])]
    public function __invoke(string $slug): Response
    {
        $cluster = $this->clusterRepository->getBySlug($slug);

        return $this->responseRenderer->render('clusters/cluster_detail.twig', [
            'cluster' => $cluster,
            'title' => $cluster->getTitle(),
        ]);
    }
}
