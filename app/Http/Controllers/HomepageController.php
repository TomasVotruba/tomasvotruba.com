<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repository\PostRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class HomepageController extends Controller
{
    /**
     * @var string[]
     */
    private const QUOTES = [
        'A seed grows with no sound, but a tree falls with a huge noise. Destruction has noise, but creation is quiet. This is the power of silence. Grow Silently',
        "If you don't want to do something, you'll find an excuse.<br>If you really do, you'll find a way.",
        'Every day is a new life to a wise man.',
        'The cave you fear to enter<br>holds the treasure you seek.',
        "You can't wait until life isn't hard anymore<br>before you decide to be happy.",
        'If you live an ordinary life, all you have is ordinary stories.<br>You have to live a life of adventurer.',
        "Many of Life's failures are people who did not realize<br>how close they were to success when they gave up",
    ];

    public function __construct(
        private readonly PostRepository $postRepository
    ) {
    }

    public function __invoke(): View
    {
        $lastPosts = $this->postRepository->fetchAll();
        $randomQuote = self::QUOTES[array_rand(self::QUOTES)];

        return \view('homepage', [
            'last_posts' => $lastPosts,
            'title' => 'Change Fast and Safely',
            'quote' => $randomQuote,
        ]);
    }
}
