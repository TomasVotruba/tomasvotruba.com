<?php declare(strict_types=1);

namespace TomasVotruba\ContribThanker\Guzzle;

use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface;
use TomasVotruba\ContribThanker\Exception\Api\ApiException;

final class ResponseFormatter
{
    /**
     * @return mixed[]
     */
    public function formatResponseToJson(ResponseInterface $response, string $originalUrl): array
    {
        if ($response->getStatusCode() >= 400) {
            throw new ApiException(sprintf(
                'Response to "%s" failed with code %d and message: "%s"',
                $originalUrl,
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
        }

        return Json::decode((string) $response->getBody(), Json::FORCE_ARRAY);
    }
}
