<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repository\PostRepository;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

final class PostMdController extends Controller
{
    public function __invoke(string $slug, PostRepository $postRepository): Response
    {
        $post = $postRepository->getBySlug($slug);

        $response = response()->view('post-md', [
            'post' => $post,
        ])->header('Content-Type', 'text/plain');

        return $response;
    }
}
