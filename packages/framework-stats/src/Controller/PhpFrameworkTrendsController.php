<?php

declare(strict_types=1);

namespace TomasVotruba\FrameworkStats\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\FrameworkStats\ValueObject\Option;

final class PhpFrameworkTrendsController extends AbstractController
{
    private PostRepository $postRepository;

    /**
     * @var mixed[]
     */
    private array $phpFrameworkTrends = [];

    public function __construct(PostRepository $postRepository, ParameterProvider $parameterProvider)
    {
        $this->postRepository = $postRepository;
        $this->phpFrameworkTrends = $parameterProvider->provideArrayParameter(Option::PHP_FRAMEWORK_TRENDS);
    }

    /**
     * @Route(path="php-framework-trends", name="php_framework_trends")
     */
    public function __invoke(): Response
    {
        $promoPost = $this->postRepository->get(202);

        return $this->render('php-framework-trends.twig', [
            'title' => 'PHP Framework Trends',
            'promo_post' => $promoPost,
            'php_framework_trends' => $this->phpFrameworkTrends,
        ]);
    }
}
