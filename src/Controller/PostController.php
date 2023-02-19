<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use TomasVotruba\Blog\Repository\PostRepository;

final class PostController extends Controller
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    public function __invoke(string $slug): View
    {
        $post = $this->postRepository->getBySlug($slug);
        $previousPost = $this->postRepository->findPreviousPost($post);
        return \view('blog/post_detail', [
            'post' => $post,
            'previous_post' => $previousPost,
            'title' => $post->getTitle(),
        ]);
    }
}
