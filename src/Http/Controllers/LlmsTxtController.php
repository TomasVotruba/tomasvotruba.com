<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repository\PostRepository;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

final class LlmsTxtController extends Controller
{
    public function __construct(
        private readonly PostRepository $postRepository
    ) {
    }

    public function __invoke(): Response
    {
        $response = response()->view('llms-txt', [
            'posts' => $this->postRepository->fetchAll(),
        ])->header('Content-Type', 'text/plain');

        return $response;
    }
}
