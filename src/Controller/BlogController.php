<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Illuminate\Routing\Controller;
use TomasVotruba\Blog\Repository\PostRepository;

final class BlogController extends Controller
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    public function __invoke(): \Illuminate\Contracts\View\View
    {
        return \view('blog', [
            'title' => 'Blog',
            'posts' => $this->postRepository->fetchAllEnglish(),
        ]);
    }
}
