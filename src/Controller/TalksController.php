<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TalksController extends AbstractController
{
    /**
     * @var mixed[]
     */
    private $talks = [];

    /**
     * @var mixed[]
     */
    private $talksFeedback = [];

    public function __construct(array $talks, array $talksFeedback)
    {
        $this->talks = $talks;
        $this->talksFeedback = $talksFeedback;
    }

    /**
     * @Route(path="talks", name="talks")
     */
    public function __invoke(): Response
    {
        return $this->render('talks.twig', [
            'title' => 'Talks',
            'talks' => $this->talks,
            'talks_feedback' => $this->talksFeedback,
        ]);
    }
}
