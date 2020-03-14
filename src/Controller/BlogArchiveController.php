<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\PostRepository;

final class BlogArchiveController extends AbstractController
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @Route(path="/archive/{year}", name="blog_archive")
     */
    public function __invoke(int $year): Response
    {
        $postByYear = $this->postRepository->fetchByYear($year);

        return $this->render('blog/archive.twig', [
            'posts' => $postByYear,
            'title' => 'Archive ' . $year,
            'year' => $year,
        ]);
    }
}
