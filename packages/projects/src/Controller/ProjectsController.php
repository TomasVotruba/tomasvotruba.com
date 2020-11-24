<?php

declare(strict_types=1);

namespace TomasVotruba\Projects\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Projects\PackageFactory\VendorPackagesFactory;

final class ProjectsController extends AbstractController
{
    private VendorPackagesFactory $vendorPackagesFactory;

    public function __construct(VendorPackagesFactory $vendorPackagesFactory)
    {
        $this->vendorPackagesFactory = $vendorPackagesFactory;
    }

    /**
     * @Route(path="projects", name="projects")
     */
    public function __invoke(): Response
    {
        return $this->render('projects/projects.twig', [
            'title' => 'Projects',
            'symplify_packages' => $this->vendorPackagesFactory->createPackagesByVendor('symplify'),
            'other_packages' => $this->vendorPackagesFactory->createPackagesByPackageNames([
                'knplabs/doctrine-behaviors',
                'cpliakas/git-wrapper',
            ]),
        ]);
    }
}
