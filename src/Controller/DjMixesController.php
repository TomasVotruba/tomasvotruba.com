<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Website\ValueObject\DjMix;

final class DjMixesController extends AbstractController
{
    /**
     * @see https://www.mixcloud.com/developers/
     * @var string
     */
    private const MIXES_API_RESOURCE = 'https://api.mixcloud.com/tomasvotruba/cloudcasts?limit=100';

    #[Route(path: '/dj-mixes')]
    public function __invoke(): Response
    {
        $mixesJsonContent = FileSystem::read(self::MIXES_API_RESOURCE);
        $mixesJson = Json::decode($mixesJsonContent, Json::FORCE_ARRAY);

        $djMixes = [];

        foreach ($mixesJson['data'] as $mixData) {
            $imageUrl = $mixData['pictures']['large'];
            $djMixes[] = new DjMix($mixData['name'], $mixData['url'], $imageUrl);
        }

        return $this->render('dj-mixes.twig', [
            'title' => 'DJ Schmutzka - Mixes',
            'dj_mixes' => $djMixes,
        ]);
    }
}
