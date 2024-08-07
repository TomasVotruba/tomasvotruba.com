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
        // 2024
        'He who blames others has a long way to go on his journey. He who blames himself is halfway there. He who blames no one has arrived.',
        'A seed grows with no sound, but a tree falls with a huge noise. Destruction has noise, but creation is quiet. This is the power of silence. Grow Silently',
        "If you don't want to do something, you'll find an excuse.<br>If you really do, you'll find a way.",
        'Every day is a new life<br>to a wise man.',
        'The cave you fear to enter<br>holds the treasure you seek.',
        "You can't wait until life isn't hard anymore<br>before you decide to be happy.",
        // source https://twitter.com/syaranza_/status/1403034104845660161
        // 'Clean code is a code, that junior and senior can understand in the same amount of time'
        // https://twitter.com/votrubat/status/1275863668878688257
        "If you can't explain it to a six-year-old,<br>you don't understand it yourself.",
        // Rainer Maria Rilke
        'Let everything happen to you<br>Beauty and terror<br>Just keep going<br>No feeling is final',
        'If you live an ordinary life, all you have is ordinary stories.<br>You have to live a life of adventurer.',
        // bukowksi
        'Find what you love and let it kill you. Let it drain you of your all. Let it cling onto your back and weigh you down into eventual nothingness.<br>Let it kill you and let it devour your remains. For all things will kill you,<br>both slowly and fastly, but it is much better to be killed by a lover.',
        // Thomas A. Edison
        "Many of Life's failures<br>are people who did not realize<br>how close they were to success<br>when they gave up",
        "Do what you feel in your heart to be right.<br>For you'll be criticized anyway",
    ];

    public function __construct(
        private readonly PostRepository $postRepository
    ) {
    }

    public function __invoke(): View
    {
        $lastPosts = $this->postRepository->fetchLast(5);

        return \view('homepage', [
            'last_posts' => $lastPosts,
            'title' => 'Change Fast and Safely',
            'quote' => $this->getRandomQuote(),
        ]);
    }

    private function getRandomQuote(): string
    {
        $randomQuoteKey = array_rand(self::QUOTES);
        return self::QUOTES[$randomQuoteKey];
    }
}
