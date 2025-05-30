<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repository\PostRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

final class PostController extends Controller
{
    public function __invoke(string $slug, PostRepository $postRepository): View|RedirectResponse
    {
        $post = $postRepository->getBySlug($slug);

        if ($post->getAlias() && $post->getAlias() !== $slug) {
            return redirect('/blog/' . $post->getAlias());
        }

        return \view('post', [
            'post' => $post,
            'title' => $post->getClearTitle(),
        ]);
    }
}
