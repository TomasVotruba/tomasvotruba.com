<?php

declare(strict_types=1);

namespace TomasVotruba\Website\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\PostRepository;
use TomasVotruba\Blog\ValueObject\Post;
use TomasVotruba\Website\ValueObject\RouteName;

/**
 * This is for permalink of most used cluster - Monorepo - From Zero to Hero so people can discovery it before moving to
 * a book
 */
final class MonorepoClusterController extends AbstractController
{
    /**
     * @var int
     */
    private const MONOREPO_POST_IDS = [287, 286, 283, 256, 223, 69, 25, 82, 124, 138, 143, 160, 161, 182];

    public function __construct(
        private PostRepository $postRepository,
    ) {
    }

    #[Route(path: '/cluster/monorepo-from-zero-to-hero', name: RouteName::CLUSTER_MONOREPO)]
    public function __invoke(): Response
    {
        return $this->render('blog/cluster_detail.twig', [
            'title' => 'Monorepo: From Zero to Hero',
            'posts' => $this->getPosts(),
        ]);
    }

    /**
     * @return Post[]
     */
    private function getPosts(): array
    {
        $posts = [];

        foreach (self::MONOREPO_POST_IDS as $monorepoPostId) {
            $posts[] = $this->postRepository->get($monorepoPostId);
        }

        return $posts;
    }
}
