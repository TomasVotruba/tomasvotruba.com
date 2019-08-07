<?php declare(strict_types=1);

namespace TomasVotruba\Website\Twig;

use Symplify\Statie\Contract\Templating\FilterProviderInterface;

final class MonorepoMatrixFilterProvider implements FilterProviderInterface
{
    /**
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            /** @var mixed[] $packagesData */
            'fetch_versions' => function (array $packagesData): array {
                $versions = [];
                foreach ($packagesData as $packageData) {
                    $versions = array_merge($versions, array_keys($packageData['downloads_minor']));
                }

                rsort($versions);

                return array_unique($versions);
            },
        ];
    }
}
