<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Blog\ValueObject\Post;

final class PostController extends AbstractController
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @Route(path="/blog/{slug}", name="post_detail", requirements={"slug"="\d+\/\d+.+"})
     */
    public function __invoke(string $slug): Response
    {
        $post = $this->postRepository->getBySlug($slug);

        return $this->render('blog/post.twig', [
            'post' => $post,
            'title' => $post->getTitle(),
        ]);
    }
}
