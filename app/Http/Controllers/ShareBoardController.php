<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repository\PostRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class ShareBoardController extends Controller
{
    public function __construct(
        private PostRepository $postRepository
    ) {
    }

    public function __invoke(): View
    {
        $posts = $this->postRepository->fetchAll();

        return \view('share_board', [
            'title' => 'Share board',
            'randomPosts' => $posts,
        ]);
    }
}
