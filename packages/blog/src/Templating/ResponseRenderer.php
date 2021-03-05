<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Templating;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class ResponseRenderer
{
    public function __construct(
        private Environment $environment
    ) {
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function render(string $view, array $parameters = []): Response
    {
        $content = $this->environment->render($view, $parameters);
        $response = new Response();
        $response->setContent($content);
        return $response;
    }
}
