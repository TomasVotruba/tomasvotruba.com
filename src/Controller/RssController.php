<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RssController extends AbstractController
{
    /**
     * @Route(path="rss.xml", name="rss")
     */
    public function __invoke(): Response
    {
        return $this->render('rss.twig');
    }
}
