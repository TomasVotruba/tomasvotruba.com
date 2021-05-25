<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BankController extends AbstractController
{
    #[Route(path: '/bank')]
    public function __invoke(): Response
    {
        return $this->render('blog/bank.twig', [
            'title' => 'Bank Details',
        ]);
    }
}
