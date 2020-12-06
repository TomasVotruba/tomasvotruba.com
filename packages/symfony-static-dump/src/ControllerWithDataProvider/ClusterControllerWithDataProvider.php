<?php

declare(strict_types=1);

namespace TomasVotruba\SymfonyStaticDump\ControllerWithDataProvider;

use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use TomasVotruba\Blog\Controller\ClusterController;
use TomasVotruba\Blog\Controller\PostController;
use TomasVotruba\Blog\Repository\ClusterRepository;
use TomasVotruba\Blog\Repository\PostRepository;

final class ClusterControllerWithDataProvider implements ControllerWithDataProviderInterface
{
    public function __construct(private ClusterRepository $clusterRepository)
    {
    }

    public function getControllerClass(): string
    {
        return ClusterController::class;
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

        foreach ($this->clusterRepository->getClusters() as $cluster) {
            $slugs[] = $cluster->getSlug();
        }

        return $slugs;
    }
}
