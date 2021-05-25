<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\ClusterRepository;

use TomasVotruba\Website\ValueObject\RouteName;

final class ClustersController extends AbstractController
{
    public function __construct(
        private ClusterRepository $clusterRepository,
    ) {
    }

    #[Route(path: 'clusters', name: RouteName::CLUSTERS)]
    public function __invoke(): Response
    {
        return $this->render('blog/clusters.twig', [
            'title' => 'Clusters',
            'clusters' => $this->clusterRepository->getClusters(),
        ]);
    }
}
