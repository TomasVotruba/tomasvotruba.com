<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\Design;
use App\Repository\PostRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class BlogController extends Controller
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    public function __invoke(): View
    {
        return \view('blog', [
            'title' => Design::BLOG_TITLE,
            'posts' => $this->postRepository->fetchAll(),
        ]);
    }
}
