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
    public function userIndexNavbar($variable, EntityManagerInterface $entityManager): Response
    {

        $video = $entityManager->getRepository(Video::class)->findAll();

        // si le produit n'est pas trouvé 
        if(!$video){
            // lancer une erreur
            throw $this->createNotFoundException(
                'Aucun produit trouvé'
            );
        }

        return $this->render('user_account/index.html.twig', [
            'variable' => $variable,
            'video' => $video
        ]);
    }
}
