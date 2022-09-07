<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Article;
use App\Repository\ArticleRepository;

class BlogController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/blog', name: 'app_blog')]
    public function index(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/', name: 'home')]
    public function home() {
        $user = "Gugusteh";
        $data = [
            'title' => "Homepage",
            'user' => $user,
        ];
        return $this->render('blog/home.html.twig', $data);
    }
    
    #[Route('/article/{id}', name: 'article_display')]
    public function articleDisplay(Article $article) {
        $data = [
           'article' => $article,
        ];
        return $this->render('blog/article.html.twig', $data);
    }
}
