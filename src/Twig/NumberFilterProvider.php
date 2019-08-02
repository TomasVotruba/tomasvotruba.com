<?php declare(strict_types=1);

namespace TomasVotruba\Website\Twig;

use Symplify\Statie\Contract\Templating\FilterProviderInterface;

final class NumberFilterProvider implements FilterProviderInterface
{
    /**
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            /** @var mixed $talksByTopic */
            'millions' => function (int $number) {
                if ($number > 10 ** 5) {
                    return round($number / (10 ** 6), 1) . ' mil.';
                }

                return number_format($number, 0, '', ' ');
            }
        ];
    }
}
