<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\ClusterRepository;

use TomasVotruba\Website\ValueObject\RouteName;

final class ClusterController extends AbstractController
{
    public function __construct(
        private ClusterRepository $clusterRepository,
    ) {
    }

    #[Route(path: '/cluster/{slug}', name: RouteName::CLUSTER_DETAIL, requirements: [
        'slug' => '[\w\-]+',
    ])]
    public function __invoke(string $slug): Response
    {
        $cluster = $this->clusterRepository->getBySlug($slug);

        return $this->render('blog/cluster_detail.twig', [
            'cluster' => $cluster,
            'title' => $cluster->getTitle(),
        ]);
    }
}
