<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\ClusterRepository;

final class ClusterController extends AbstractController
{
    public function __construct(private ClusterRepository $clusterRepository)
    {
    }

    #[Route('/cluster/{slug}', name: 'cluster_detail', requirements: [
        'slug' => '[\w\-]+',
    ])]
    public function __invoke(string $slug): Response
    {
        $cluster = $this->clusterRepository->getBySlug($slug);

        return $this->render('clusters/cluster_detail.twig', [
            'cluster' => $cluster,
            'title' => $cluster->getTitle(),
        ]);
    }
}
