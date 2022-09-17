<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;


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
        $user = $this->getUser()->getUsername();
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
            'articleForm' => $form,
            'edit' => $edit,
        ]);
    }

    #[Route('/article/{id}', name: 'article_display')]
    public function articleDisplay(Article $article, Request $request): Response
    {
        // Comment creation
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->HandleRequest($request);
        if($commentForm->isSubmitted() && $commentForm->isValid() && !is_null($this->getUser())) {
            $comment->setArticle($article);
            $this->createComment($comment, $request);
        }

        //Article view data
        $data = array(
           'article' => $article,
           'commentForm' => $commentForm,
        );
        return $this->renderForm('blog/article.html.twig', $data);
    }

    public function createComment(Comment $comment, Request $request): Void
    {
        $repo = new CommentRepository($this->doctrine);
        $comment->setCreatedAt(new \DateTimeImmutable());
        $comment->setAuthor($this->getUser()->getUsername());
        $repo->add($comment, true);
    }
}
