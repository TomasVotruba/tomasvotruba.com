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
<<<<<<< HEAD
        return $this->render('blog/post_detail.twig', [
=======
        return $this->render('blog/post.twig', [
>>>>>>> 4cfe9a3a0... [PHP 8.0 Rector] Apply on the rest of code
            'post' => $post,
            'title' => $post->getTitle(),
        ]);
    }
}
