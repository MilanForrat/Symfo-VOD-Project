<?php

namespace App\Controller;

// appel de doctrine pour communiquer avec la BDD

use App\Class\Mail;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Article;
use App\Entity\Event;
use App\Entity\Video;
use App\Entity\Header;

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
        $headers = $entityManager->getRepository(Header::class)->findAll();
        // débugger 
        // var_dump($video); die;

        return $this->render('home/index.html.twig', [
            'article' => $article,
            'video' => $video,
            'headers'=>$headers,
            'videosInHomepage'=> $entityManager->getRepository(Video::class)->findByIsHomepage(true),
            'articlesInHomepage'=>$entityManager->getRepository(Article::class)->findByIsHomepage(true),
            'eventsInHomepage' => $entityManager->getRepository(Event::class)->findByIsHomepage(true),
        ]);
    }
}
