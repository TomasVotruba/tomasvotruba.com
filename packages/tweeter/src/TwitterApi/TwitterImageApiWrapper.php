<?php

declare(strict_types=1);

namespace TomasVotruba\Tweeter\TwitterApi;

use Nette\Utils\FileSystem;
use TomasVotruba\Tweeter\Exception\ShouldNotHappenException;
use TomasVotruba\Tweeter\ValueObject\TwitterApi;

final class TwitterImageApiWrapper
{
    /**
     * @var string
     * @see https://developer.twitter.com/en/docs/media/upload-media/api-reference/post-media-upload
     */
    private const IMAGE_UPLOAD_URL = 'https://upload.twitter.com/' . TwitterApi::API_VERSION . '/media/upload.json';

    /**
     * 5 MB
     * @var int
     */
    private const MAX_IMAGE_FILE_SIZE = 5 * 1024 * 1024;

    private TwitterApiCaller $twitterApiCaller;

    public function __construct(TwitterApiCaller $twitterApiCaller)
    {
        $this->twitterApiCaller = $twitterApiCaller;
    }

    /**
     * Ref: https://developer.twitter.com/en/docs/media/upload-media/api-reference/post-media-upload and
     * https://developer.twitter.com/en/docs/tweets/post-and-engage/api-reference/post-statuses-update.html "media_ids"
     */
    public function uploadImage(string $imageFile): array
    {
        $headers = get_headers($imageFile, 1);
        if (! is_array($headers)) {
            throw new ShouldNotHappenException();
        }

        $fileSizeInBytes = $headers['Content-Length'];
        $mediaType = $headers['Content-Type'];

        if ($fileSizeInBytes > self::MAX_IMAGE_FILE_SIZE) {
            throw new ShouldNotHappenException(sprintf(
                'File "%s" %d too big. Make it smaller under 5 MB',
                $imageFile,
                $fileSizeInBytes
            ));
        }

        $response = $this->twitterApiCaller->callPost(self::IMAGE_UPLOAD_URL, [
            'command' => 'INIT',
            'total_bytes' => $fileSizeInBytes,
            'media_type' => $mediaType,
        ]);

        $mediaId = $response['media_id'];
        $this->runImageAppend($imageFile, $mediaId);

        return $this->twitterApiCaller-> callPost(self::IMAGE_UPLOAD_URL, [
            'command' => 'FINALIZE',
            'media_id' => $mediaId,
        ]);
    }

    private function runImageAppend(string $imageFile, int $mediaId): void
    {
        $based64EncodedBinaryFile = base64_encode(FileSystem::read($imageFile));

        // just 200 response
        $this->twitterApiCaller->callPost(self::IMAGE_UPLOAD_URL, [
            'command' => 'APPEND',
            'media_id' => $mediaId,
            'media' => $based64EncodedBinaryFile,
            'segment_index' => 0,
        ]);
    }
}
