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
     * @var string
     */
    private const QUOTES = [
        "If you don't want to do something, you'll find an excuse.<br>If you really do, you'll find a way.",
        'Every day is a new life to a wise man.',
    ];

    #[Route(path: '/', name: RouteName::HOMEPAGE)]
    public function __invoke(): Response
    {
        $quotes = self::QUOTES;

        $randomQuoteKey = array_rand($quotes);
        $quote = $quotes[$randomQuoteKey];

        return $this->render('homepage.twig', [
            'title' => 'PHP Trainings, Rectoring and Posts',
            'quote' => $quote,
        ]);
    }
}
