<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\PostRepository;

final class HomepageController extends AbstractController
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @Route(path="/", name="homepage")
     */
    public function __invoke(): Response
    {
        return $this->render('index.twig', [
            'posts' => $this->postRepository->fetchAllEnglishNonDeprecated(),
            'post_homepage_limit' => 50,
            'title' => 'PHP Lectures, Mentoring, Communities and Posts',
        ]);
    }
}
