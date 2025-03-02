<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    // #[Route('/article', name: 'app_article')]
    // public function index(): Response
    // {
    //     return $this->render('article/index.html.twig', [
    //         'controller_name' => 'ArticleController',
    //     ]);
    // }

    // permet de trouver les articles par slug
    #[Route('/article/{slug}', name: 'app_article_details')]
    public function viewArticleDetails(EntityManagerInterface $entityManager,  $slug): Response
    {
    
        $article = $entityManager->getRepository(Article::class)->findOneBySlug($slug);

        // si on ne trouve pas de vidéo on redirige à la page d'accueil
        if(!$article){
            return $this->redirectToRoute('app_home');
        }

        return $this->render('article/details.html.twig', [
            // on passe l'objet en entier afin d'accéder à tous ses détails
            'article' => $article,
        ]);
    }

        // page où l'on retrouve tous les articles
        #[Route('/article', name: 'app_all_articles')]
        public function viewAllArticles(EntityManagerInterface $entityManager): Response
        {
        
            $article = $entityManager->getRepository(Article::class)->findAll();
    
            // si on ne trouve pas d'articles on redirige à la page d'accueil
            if(!$article){
                return $this->redirectToRoute('app_home');
            }
    
            return $this->render('article/article_index.html.twig', [
                // on passe l'objet en entier afin d'accéder à tous ses détails
                'article' => $article,
            ]);
        }
}
