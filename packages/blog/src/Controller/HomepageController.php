<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\PostRepository;

final class HomepageController extends AbstractController
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    #[Route('/', name: 'homepage')]
    public function __invoke(): Response
    {
        return $this->render('index.twig', [
            'posts' => $this->postRepository->fetchAllEnglishNonDeprecated(),
            'post_homepage_limit' => 50,
            'title' => 'PHP Lectures, Mentoring, Communities and Posts',
        ]);
    }
}
