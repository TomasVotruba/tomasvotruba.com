<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repository\PostRepository;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

final class PostMdController extends Controller
{
    public function __invoke(string $slug, PostRepository $postRepository): View
    {
        $post = $postRepository->getBySlug($slug);

        return view('post-md', [
            'post' => $post,
        ]);
    }
}
