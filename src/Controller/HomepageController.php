<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Website\ValueObject\RouteName;

final class HomepageController extends AbstractController
{
    /**
     * @var string[]
     */
    private const QUOTES = [
        "If you don't want to do something, you'll find an excuse.<br>If you really do, you'll find a way.",
        'Every day is a new life to a wise man.',
        'The cave you fear to enter<br>holds the treasure you seek.',
        "You can't wait until life isn't hard anymore<br>before you decide to be happy.",
        // source https://twitter.com/syaranza_/status/1403034104845660161
        // 'Clean code is a code, that junior and senior can understand in the same amount of time'
        // https://twitter.com/votrubat/status/1275863668878688257
        "If you can't explain it to a six-year-old,<br>you don't understand it yourself.",
        // Rainer Maria Rilke
        'Let everything happen to you<br>Beauty and terror<brJust keep going<br>No feeling is final',
        'If you live an ordinary life, all you have is ordinary stories.<br>You have to live a life of adventurer.'
    ];

    public function __construct(
        private readonly PostRepository $postRepository
    ) {
    }

    #[Route(path: '/', name: RouteName::HOMEPAGE)]
    public function __invoke(): Response
    {
        $lastPosts = $this->postRepository->fetchLast(5);

        return $this->render('homepage.twig', [
            'last_posts' => $lastPosts,
            'title' => 'Change Fast and Safely',
            'quote' => $this->getRandomQuote(),
            'next_month' => $this->getNextMonthName(),
        ]);
    }

    private function getRandomQuote(): string
    {
        $randomQuoteKey = array_rand(self::QUOTES);
        return self::QUOTES[$randomQuoteKey];
    }

    private function getNextMonthName(): string
    {
        $nextMonthTime = strtotime('+50 days');
        return date('F', $nextMonthTime);
    }
}
