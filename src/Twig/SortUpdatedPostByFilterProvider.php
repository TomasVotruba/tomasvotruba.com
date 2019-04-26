<?php declare(strict_types=1);

namespace TomasVotruba\Website\Twig;

use Nette\Utils\DateTime;
use Symplify\Statie\Contract\Templating\FilterProviderInterface;
use Symplify\Statie\Renderable\File\PostFile;

final class SortUpdatedPostByFilterProvider implements FilterProviderInterface
{
    /**
     * @return callable[]
     */
    public function provide(): array
    {
        return [
            /** @var PostFile[] $posts */
            'sort_posts_by_updated_at' => function (array $posts) {
                usort($posts, function (PostFile $firstPost, PostFile $secondPost) {
                    $secondUpdatedSince = DateTime::from($secondPost->getOption('updated_since'));
                    $firstUpdatedSince = DateTime::from($firstPost->getOption('updated_since'));

                    return $secondUpdatedSince <=> $firstUpdatedSince;
                });

                return $posts;
            },
        ];
    }
}
