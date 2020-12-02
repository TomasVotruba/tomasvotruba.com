<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Repository;

use TomasVotruba\Blog\DataProvider\ClusterDataProvider;
use TomasVotruba\Blog\ValueObject\Cluster;
use TomasVotruba\Website\Exception\ShouldNotHappenException;

final class ClusterRepository
{
    /**
     * @var Cluster[]
     */
    private array $clusters = [];

    public function __construct(ClusterDataProvider $clusterDataProvider)
    {
        $this->clusters = $clusterDataProvider->provide();
    }

    /**
     * @return Cluster[]
     */
    public function getClusters(): array
    {
        return $this->clusters;
    }

    public function getBySlug(string $slug): Cluster
    {
        foreach ($this->clusters as $cluster) {
            if ($cluster->getSlug() === $slug) {
                return $cluster;
            }
        }

        $message = sprintf('Cluster for "%s" was not found', $slug);
        throw new ShouldNotHappenException($message);
    }
}
