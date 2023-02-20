<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Illuminate\Routing\Controller;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Nette\Utils\Strings;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @see Inspiration https://og.beyondco.de/Fix%20your%20Laravel%20exceptions%20with%20AI.png?theme=light&md=1&body=Take%20advantage%20of%20OpenAI%20to%20enhance%20your%20Laravel%20error%20pages%20with%20AI-powered%20solutions.&fontSize=125px&isPost=1&author=Marcel%20Pociot&authorAvatar=https%3A%2F%2Fbeyondco.de%2Fimg%2Fmarcel.jpg&readDuration=11%20minute%20read
 */
final class PostImageController extends Controller
{
    public function __invoke(Request $request): BinaryFileResponse
    {
        // @see https://imagine.readthedocs.io/en/stable/
        $title = $request->get('title');
        $rgb = new RGB();
        $imagine = new Imagine();
        $imageFilePath = __DIR__ . '/../../public/assets/images/posts/thumbnail/' . Strings::webalize($title) . '.png';
        $box = new Box(2040, 1117);
        $image = $imagine->create($box);
        // downloaded from https://fonts.google.com/specimen/Source+Sans+Pro?query=Source+Sans+Pro
        $blackColor = $rgb->color('000000');
        $blackHeadlineFont = $imagine->font(
            __DIR__ . '/../../public/assets/fonts/SourceSansPro-Bold.ttf',
            100,
            $blackColor
        );
        $drawer = $image->draw();
        $drawer->text($title, $blackHeadlineFont, new Point(130, 340), 0, 1800);

        $greenColor = $rgb->color('1a8917');
        $greenTextFont = $imagine->font(
            __DIR__ . '/../../public/assets/fonts/Inter-VariableFont_slnt,wght.ttf',
            40,
            $greenColor
        );
        //$drawer = $image->draw();
        $drawer->text('Written by Tomas Votruba', $greenTextFont, new Point(130, 870), 0, 400);
        // add my face :)
        $faceImage = $imagine->open(__DIR__ . '/../../public/assets/images/tomas_votruba_circle.jpg');
        $faceImage->resize(new Box(200, 200));

        $image->paste($faceImage, new Point(1700, 800));
        $image->save($imageFilePath);
        return new BinaryFileResponse($imageFilePath);
    }
}
