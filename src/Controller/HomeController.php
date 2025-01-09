<?php

namespace App\Controller;

// appel de doctrine pour communiquer avec la BDD
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Article;
use App\Entity\Video;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $video = $entityManager->getRepository(Video::class)->findAll();
        $article = $entityManager->getRepository(Article::class)->findAll();
        // débugger 
        // var_dump($video); die;

        return $this->render('home/index.html.twig', [
            'article' => $article,
            'video' => $video,
        ]);
    }
}
