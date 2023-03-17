<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enum\FontFile;
use Illuminate\Routing\Controller;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\FontInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webmozart\Assert\Assert;

/**
 * @see Inspiration https://og.beyondco.de/Fix%20your%20Laravel%20exceptions%20with%20AI.png?theme=light&md=1&body=Take%20advantage%20of%20OpenAI%20to%20enhance%20your%20Laravel%20error%20pages%20with%20AI-powered%20solutions.&fontSize=125px&isPost=1&author=Marcel%20Pociot&authorAvatar=https%3A%2F%2Fbeyondco.de%2Fimg%2Fmarcel.jpg&readDuration=11%20minute%20read
 */
final class ThumbnailController extends Controller
{
    public function __construct(
        private readonly Imagine $imagine
    ) {
    }

    public function __invoke(string $title): BinaryFileResponse
    {
        // @see https://imagine.readthedocs.io/en/stable/
        $thumbnailDirectory = __DIR__ . '/../../../storage/app/public/thumbnail/';

        // ensure directory exists
        if (! is_dir($thumbnailDirectory)) {
            FileSystem::createDir($thumbnailDirectory);
        }

        Assert::directory($thumbnailDirectory);

        $imageFilePath = $thumbnailDirectory . '/' . Strings::webalize($title) . '.png';

        // on the fly
        if (! file_exists($imageFilePath)) {
            $this->createImage($title, $imageFilePath);
        }

        return response()->file($imageFilePath);
    }

    private function createFont(string $fontFamilyFile, string $hexColor, int $fontSize): FontInterface
    {
        Assert::fileExists($fontFamilyFile);

        $rgb = new RGB();
        $color = $rgb->color($hexColor);

        return $this->imagine->font($fontFamilyFile, $fontSize, $color);
    }

    private function createImage(string $title, string $imageFilePath): void
    {
        $box = new Box(2040, 1117);
        $image = $this->imagine->create($box);
        $drawer = $image->draw();

        $blackFont = $this->createFont(FontFile::SOURCE_SANS, '000000', 100);
        $drawer->text($title, $blackFont, new Point(130, 340), 0, 1800);

        $greenFont = $this->createFont(FontFile::INTER, '1a8917', 40);
        $drawer->text('Written by Tomas Votruba', $greenFont, new Point(130, 870), 0, 400);

        // add my face :)
        $faceImage = $this->imagine->open(__DIR__ . '/../../../public/assets/images/tomas_votruba_circle.jpg');
        $faceImage->resize(new Box(200, 200));

        $image->paste($faceImage, new Point(1700, 800));
        $image->save($imageFilePath);
    }
}
