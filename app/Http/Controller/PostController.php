<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Repository\PostRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class PostController extends Controller
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    public function __invoke(string $slug): View
    {
        $post = $this->postRepository->getBySlug($slug);

        return \view('post', [
            'post' => $post,
            'title' => $post->getClearTitle(),
        ]);
    }
}
