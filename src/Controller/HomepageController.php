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
        // 'Clean code is a code, that junior and senior can understand in the same amount of time'
        // https://twitter.com/votrubat/status/1275863668878688257
    ];

    #[Route(path: '/', name: RouteName::HOMEPAGE)]
    public function __invoke(): Response
    {
        $randomQuoteKey = array_rand(self::QUOTES);
        $quote = self::QUOTES[$randomQuoteKey];

        return $this->render('homepage.twig', [
            'title' => 'Change Fast and Safe',
            'quote' => $quote,
        ]);
    }
}
