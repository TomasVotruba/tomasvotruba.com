<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\PostRepository;

final class PostController extends AbstractController
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    #[Route('/blog/{slug}', name: 'post_detail', requirements: [
        'slug' => '\d+\/\d+.+',
    ])]
    public function __invoke(string $slug): Response
    {
        $post = $this->postRepository->getBySlug($slug);
        return $this->render('blog/post_detail.twig', [
            'post' => $post,
            'title' => $post->getTitle(),
        ]);
    }
}
