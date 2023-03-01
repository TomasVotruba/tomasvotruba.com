<?php

declare(strict_types=1);

namespace App\Http\Controller;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use TomasVotruba\Website\Repository\PostRepository;

final class BlogController extends Controller
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    public function __invoke(): View
    {
        return \view('blog', [
            'title' => 'Blog',
            'posts' => $this->postRepository->fetchAll(),
        ]);
    }
}
