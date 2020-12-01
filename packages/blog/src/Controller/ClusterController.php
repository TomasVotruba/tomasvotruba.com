<?php

declare(strict_types=1);

namespace TomasVotruba\Blog\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TomasVotruba\Blog\Repository\ClusterRepository;

final class ClusterController extends AbstractController
{
    private ClusterRepository $clusterRepository;

    public function __construct(ClusterRepository $clusterRepository)
    {
        $this->clusterRepository = $clusterRepository;
    }

    /**
     * @Route(path="/cluster/{slug}", name="cluster_detail", requirements={"slug"="\d+\/\d+.+"})
     */
    public function __invoke(string $slug): Response
    {
        dump($slug);
        die;
//        $post = $this->postRepository->getBySlug($slug);
//
//        return $this->render('blog/post.twig', [
//            'post' => $post,
//            'title' => $post->getTitle(),
//        ]);
    }
}
