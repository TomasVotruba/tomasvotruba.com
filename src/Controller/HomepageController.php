<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Website\ValueObject\RouteName;

final class HomepageController extends AbstractController
{
    /**
     * @var string[]
     */
    private const QUOTES = [
        "If you don't want to do something, you'll find an excuse.<br>If you really do, you'll find a way.",
        'Every day is a new life to a wise man.',
        "You can't wait until life isn't hard anymore before you decide to be happy.",
        // source https://twitter.com/syaranza_/status/1403034104845660161
        // 'Clean code is a code, that junior and senior can understand in the same amount of time'
        // https://twitter.com/votrubat/status/1275863668878688257
    ];

    #[Route(path: '/', name: RouteName::HOMEPAGE)]
    public function __invoke(): Response
    {
        return $this->render('homepage.twig', [
            'title' => 'Change Fast and Safe',
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
        $nextMonthTime = strtotime('+45 days');
        return date('F', $nextMonthTime);
    }
}
