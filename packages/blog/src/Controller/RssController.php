<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Controller;

use DateTimeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Blog\Templating\ResponseRenderer;
use TomasVotruba\Blog\ValueObject\Post;
use TomasVotruba\Website\ValueObject\RouteName;

final class RssController
{
    public function __construct(
        private PostRepository $postRepository,
        private ResponseRenderer $responseRenderer
    ) {
    }

    #[Route(path: 'rss.xml', name: RouteName::RSS)]
    public function __invoke(): Response
    {
        $posts = $this->postRepository->fetchForRss();

        return $this->responseRenderer->render('rss.twig', [
            'posts' => $posts,
            'most_recent_post_date_time' => $this->getMostRecentPostDateTime($posts),
        ]);
    }

    /**
     * @param Post[] $posts
     */
    private function getMostRecentPostDateTime(array $posts): DateTimeInterface
    {
        $firstPostKey = array_key_first($posts);
        $firstPost = $posts[$firstPostKey];

        return $firstPost->getDateTime();
    }
}
