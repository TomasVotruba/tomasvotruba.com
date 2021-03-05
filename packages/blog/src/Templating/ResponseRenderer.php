<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Templating;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class ResponseRenderer
{
    private Environment $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
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
