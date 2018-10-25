<?php declare(strict_types=1);

namespace TomasVotruba\ContribThanker\Guzzle;

use Nette\Utils\Json;
use Nette\Utils\Strings;
use Psr\Http\Message\ResponseInterface;
use TomasVotruba\ContribThanker\Exception\Api\ApiException;

final class ResponseFormatter
{
    /**
     * @return mixed[]
     */
    public function formatResponseToJson(ResponseInterface $response, string $originalUrl): array
    {
        if (! Strings::startsWith((string) $response->getStatusCode(), '2')) {
            throw new ApiException(sprintf(
                'Response to "%s" failed: "%s"',
                $originalUrl,
                $response->getReasonPhrase()
            ));
        }

        return Json::decode((string) $response->getBody(), Json::FORCE_ARRAY);
    }
}
