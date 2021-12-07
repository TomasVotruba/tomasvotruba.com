<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\PostRepository;

use TomasVotruba\Website\ValueObject\RouteName;

final class BlogController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    #[Route(path: 'blog', name: RouteName::BLOG)]
    public function __invoke(): Response
    {
        return $this->render('blog.twig', [
            'title' => 'Blog',
            'posts' => $this->postRepository->fetchAllEnglish(),
        ]);
    }
}
