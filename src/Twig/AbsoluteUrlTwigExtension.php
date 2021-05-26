<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Twig;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\RequestStack;
use TomasVotruba\Website\Exception\ShouldNotHappenException;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class AbsoluteUrlTwigExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    /**
     * @return array<string, string>
     */

    #[ArrayShape([
        'string' => 'string',
    ])]
    public function getGlobals(): array
    {
        $mainRequest = $this->requestStack->getMainRequest();
        if ($mainRequest === null) {
            throw new ShouldNotHappenException();
        }

        return [
            'current_absolute_url' => $mainRequest->getUri(),
        ];
    }
}
