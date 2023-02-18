<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\PostRepository;

use TomasVotruba\Blog\ValueObject\Post;
use TomasVotruba\Website\ValueObject\RouteName;

final class RssController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    #[Route(path: 'rss.xml', name: RouteName::RSS)]
    #[Route(path: 'rss-xml')]
    public function __invoke(): Response
    {
        $posts = $this->postRepository->fetchForRss();

        $response = $this->render('rss.twig', [
            'posts' => $posts,
            'most_recent_post_date_time' => $this->getMostRecentPostDateTime($posts),
        ]);

        $response->headers->set('Content-type', 'text/xml');

        return $response;
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
