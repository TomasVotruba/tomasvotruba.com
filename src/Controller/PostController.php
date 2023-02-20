<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
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

        return \view('blog/post_detail', [
            'post' => $post,
            'title' => $post->getTitle(),
        ]);
    }
}
