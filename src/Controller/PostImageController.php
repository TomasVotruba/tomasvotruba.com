<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Intervention\Image\Image;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Website\ValueObject\RouteName;

final class PostImageController extends AbstractController
{
    #[Route(path: '/thumbnail/{title}', name: RouteName::POST_IMAGE)]
    public function __invoke(Request $request): BinaryFileResponse
    {
        // @see https://imagine.readthedocs.io/en/stable/
        $title = $request->get('title');

        $rbg = new RGB();
        $blackColor = $rbg->color('000000');

        $imagine = new Imagine();

        $imageFilePath = __DIR__ . '/../../public/assets/images/posts/thumbnail/' . Strings::webalize($title) . '.png';

        $size  = new Box(2040, 1117);

        $font = $imagine->font(__DIR__ . '/../../public/assets/fonts/RobotoMono-VariableFont_wght.ttf', 130, $blackColor);

        $image = $imagine->create($size);
        $image->draw()->text(strtoupper($title), $font, new Point(120, 210));

        $font = $imagine->font(__DIR__ . '/../../public/assets/fonts/Phudu-VariableFont_wght.ttf', 130, $blackColor);
        $image->draw()->text($title, $font, new Point(200, 410));

        $image->save($imageFilePath);

        $imagine->open($imageFilePath)
            ->show('png');
    }
}
