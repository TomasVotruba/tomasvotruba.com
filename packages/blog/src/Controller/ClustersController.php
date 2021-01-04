<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Website\ValueObject\RouteName;

final class ClustersController extends AbstractController
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    #[Route(path: 'clusters', name: RouteName::CLUSTERS)]
    public function __invoke(): Response
    {
        return $this->render('clusters/clusters.twig', [
            'title' => 'Clusters',
            'posts' => $this->postRepository->fetchAllEnglishNonDeprecated(),
        ]);
    }
}
