<?php

namespace App\Controller;

use App\Entity\Video;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserAccountController extends AbstractController
{

    #[Route('/utilisateur/compte', name: 'app_user_account')]
    public function sindex(): Response
    {
        return $this->render('user_account/index.html.twig', [
        ]);
    }

    /**
     * @Route("/utilisateur/compte/{variable}", defaults={"variable" = 0})
     * @Method("GET")
     * @Template()
     */
    #[Route('/utilisateur/compte/{variable}', name: 'app_user_account_navbar')]
    public function userIndexNavbar($variable): Response
    {
        if($variable == 1){

        }else if($variable == 2){

        }else if($variable == 3){

        }else{
            return $this->render('user_account/index.html.twig', [
            ]);
        }
    }





    // #[Route('/utilisateur/compte/infos', name: 'app_user_infos')]
    // public function personalInformation(): Response
    // {
    //     return $this->render('user_account/user_informations.html.twig', [
    //     ]);
    // }
    // #[Route('/utilisateur/compte/achats', name: 'app_user_buyings')]
    // public function userAchats(EntityManagerInterface $entityManager): Response
    // {
    //     $video = $entityManager->getRepository(Video::class)->findAll();

    //     // si on ne trouve pas de vidéo on redirige à la page d'accueil
    //     if(!$video){
    //         return $this->redirectToRoute('app_user_buyings');
    //     }

    //     return $this->render('user_account/user_buyings.html.twig', [
    //         'video' => $video,
    //     ]);
    // }
}
