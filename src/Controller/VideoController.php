<?php

namespace App\Controller;

// appel de l'entité sur laquelle on souhaite modifier la BDD
use App\Entity\Video;
// appel de doctrine pour communiquer avec la BDD
use Doctrine\ORM\EntityManagerInterface;
// appel de la gestion des controllers
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// appel du système de messagerie pour confirmer le bon ajout en BDD 
use Symfony\Component\HttpFoundation\Response;
// appel des routes pour accéder aux pages
use Symfony\Component\Routing\Attribute\Route;
// appel du repository VideoRepository
use App\Repository\VideoRepository;

class VideoController extends AbstractController
{
    // ici le "/{id}" permet d'accéder dynamiquement à chaque video depuis son id
    #[Route('/video-test1/{id}', name: 'app_video_details')]
    // ajout de "(EntityManagerInterface $entityManager): Response" pour appeler les éléments de la BDD dans l'index
    // ici "int $id" me permet d'accéder à la valeur de l'id
    public function indexDeux(EntityManagerInterface $entityManager, int $id): Response
    {
        // on créer cette ligne pour stocker dans $video le résultat de la requête sql -> je cherche la vidéo ayant pour id la valeur "x"
        $video = $entityManager->getRepository(Video::class)->find($id);

        // si le produit n'est pas trouvé 
        if(!$video){
            // lancer une erreur
            throw $this->createNotFoundException(
                'Aucun produit trouvé avec cette id : '.$id
            );
        }

        return $this->render('video/index.html.twig', [
            'controller_name' => 'VideoController',
            // on demande à la vue de passer en paramètre le "name" (donc de rendre possible le fait d'accéder à la variable "name")
            'name' => $video->getName(),
        ]);
    }


    // --------------------- AUTRE MANIERE DE FAIRE : pour récupérer des infos via un id en utilisant le REPOSITORY au lieu du ENTITY Manager

    // // ici le "/{id}" permet d'accéder dynamiquement à chaque video depuis son id
    // #[Route('/video/{id}', name: 'app_video')]
    // // ajout de "(EntityManagerInterface $entityManager): Response" pour appeler les éléments de la BDD dans l'index
    // // ici "int $id" me permet d'accéder à la valeur de l'id
    // public function index(VideoRepository $videoRepository, int $id): Response
    // {
    
    // // je vais utiliser la fonction créée dans le Repository 
    // $video = $videoRepository->findOneById($id);

    //     // si le produit n'est pas trouvé 
    //     if(!$video){
    //         // lancer une erreur
    //         throw $this->createNotFoundException(
    //             'Aucun produit trouvé avec cette id : '.$id
    //         );
    //     }

    //     return $this->render('video/index.html.twig', [
    //         'controller_name' => 'VideoController',
    //         // on demande à la vue de passer en paramètre le "name" (donc de rendre possible le fait d'accéder à la variable "name")
    //         'name' => $video->getName(),
    //     ]);
    // }

    // permet de trouver les videos par slug
    #[Route('/video/{slug}', name: 'app_video_details')]
    public function viewVideoDetails(EntityManagerInterface $entityManager, $slug): Response
    {
    
        $video = $entityManager->getRepository(Video::class)->findOneBySlug($slug);

        // si on ne trouve pas de vidéo on redirige à la page d'accueil
        if(!$video){
            return $this->redirectToRoute('app_home');
        }

        return $this->render('video/details.html.twig', [
            'controller_name' => 'VideoController',
            // on passe l'objet en entier afin d'accéder à tous ses détails
            'video' => $video,
        ]);
    }

    // page où l'on retrouve toutes les video
    #[Route('/video', name: 'app_all_videos')]
    public function viewAllVideos(EntityManagerInterface $entityManager): Response
    {
    
        $video = $entityManager->getRepository(Video::class)->findAll();

        // si on ne trouve pas d'articles on redirige à la page d'accueil
        if(!$video){
            return $this->redirectToRoute('app_home');
        }

        return $this->render('video/video_index.html.twig', [
            // on passe l'objet en entier afin d'accéder à tous ses détails
            'video' => $video,
        ]);
    }

    #[Route('/add-video', name: 'create_video')]
    public function createvideo(EntityManagerInterface $entityManager): Response
    {
        // création d'une entity Video en BDD & remplissage des champs lui correspondant
        $video = new Video();
        $video->setName('test');
        $video->setSlug('video-test');
        $video->setSubtitle('subtitle : lorem ipsum dolor');
        $video->setDescription('description : lorem ipsum dolor');
        $video->setImage('video-test.png');
        // code youtube 
        $video->setTrailer('O7BrNp6gO');
        $video->setLink('https://www.youtube.com/watch?v=f3tKWYzm4uc&ab_channel=MastersofAI%26Automation');

        // on demande à doctrine de sauvegarder en BDD
        $entityManager->persist($video);

        // flush éxécute la demande de sauvegarde en BDD
        $entityManager->flush();

        // affiche un message de réussite de sauvegarde de l'élément en BDD
        return new Response('Nouvelle vidéo ajoutée '.$video->getId());
    }
}
