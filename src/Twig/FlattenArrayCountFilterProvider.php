<?php declare(strict_types=1);

namespace TomasVotruba\Website\Twig;

use Nette\Utils\Arrays;
use Symplify\Statie\Contract\Templating\FilterProviderInterface;

final class FlattenArrayCountFilterProvider implements FilterProviderInterface
{
    /**
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            /** @var mixed $items */
            'flatten_array_count' => function (array $items): int {
                $flattenItems = Arrays::flatten($items);

                return count($flattenItems);
            },
        ];
    }
}
