<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Spatie\Packagist\PackagistClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Tweeter\Exception\ShouldNotHappenException;
use TomasVotruba\Website\ValueObject\PackagistPackage;

final class ProjectsController extends AbstractController
{
    private PackagistClient $packagistClient;

    public function __construct(PackagistClient $packagistClient)
    {
        $this->packagistClient = $packagistClient;
    }

    /**
     * @Route(path="projects", name="projects")
     */
    public function __invoke(): Response
    {
        return $this->render('projects/projects.twig', [
            'title' => 'Projects',
            'symplify_packages' => $this->createPackagesByVendor('symplify'),
            'migrify_packages' => $this->createPackagesByVendor('migrify'),
        ]);
    }

    /**
     * @return PackagistPackage[]
     */
    private function createPackagesByVendor(string $vendor): array
    {
        $packagistPackages = [];

        $symplifyPackages = $this->packagistClient->getPackagesNamesByVendor($vendor);
        if ($symplifyPackages === null) {
            throw new ShouldNotHappenException();
        }

        foreach ($symplifyPackages['packageNames'] as $symplifyPackageName) {
            $packageMetadata = $this->packagistClient->getPackageMetadata($symplifyPackageName);
            if ($packageMetadata === null) {
                throw new ShouldNotHappenException();
            }

            $packageMetadata = $packageMetadata['packages'][$symplifyPackageName];
            $packageMetadata = array_pop($packageMetadata);

            $packagistPackages[] = new PackagistPackage($packageMetadata['name'], $packageMetadata['description']);

            // @todo stats
        }

        return $packagistPackages;
    }
}
