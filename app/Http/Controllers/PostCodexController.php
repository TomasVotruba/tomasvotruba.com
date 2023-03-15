<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repository\PostRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class PostCodexController extends Controller
{
    public function __construct(
        private readonly PostRepository $postRepository
    ) {
    }

    public function __invoke(): View
    {
        return view('post_codex', [
            'posts' => $this->postRepository->fetchAll(),
        ]);
    }
}
