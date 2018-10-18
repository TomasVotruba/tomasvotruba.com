<?php declare(strict_types=1);

namespace TomasVotruba\ContribThanker\Twig;

use Symplify\Statie\Contract\Templating\FilterProviderInterface;

final class ContribThankerFilterProvider implements FilterProviderInterface
{
    /**
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            'sortContributorsByName' => function (array $contributors) {
                usort($contributors, function (array $firstContributor, array $secondContributor): int {
                    return strtolower($firstContributor['name']) <=> strtolower($secondContributor['name']);
                });

                return $contributors;
            },
        ];
    }
}
