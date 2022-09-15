<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ArticleType;


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
    public function home(): Response
    {
        $user = "Gugusteh";
        $data = [
            'title' => "Homepage",
            'user' => $user,
        ];
        return $this->render('blog/home.html.twig', $data);
    }
    
    #[Route('/article/create', name: 'article_create')]
    #[Route('/article/edit/{id}', name: 'article_edit')]
    public function articleForm(Article $article = null, Request $request, ArticleRepository $repo): Response
    {
        if (is_null($article)) {
            $article = new Article();
            $edit = false;
        } else {
            $edit = true;
        }
        $form = $this->createForm(ArticleType::class, $article);
        $form->HandleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            if (is_null($article->getId())) {
                $article->setCreatedAt(new \DateTimeImmutable());
            }
            $repo->add($article, true);
            return $this->redirectToRoute('article_display', array('id' => $article->getId()));
        }

        return $this->renderForm('blog/article_form.html.twig', [
            'formArticle' => $form,
            'edit' => $edit,
        ]);
    }

    #[Route('/article/{id}', name: 'article_display')]
    public function articleDisplay(Article $article): Response
    {
        $data = [
           'article' => $article,
        ];
        return $this->render('blog/article.html.twig', $data);
    }
}
