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
    #[Route('/utilisateur/compte/informations', name: 'app_user_account')]
    public function index(): Response
    {
        return $this->render('user_account/index.html.twig', [
        ]);
    }

    #[Route('/utilisateur/compte/factures', name: 'app_user_account_invoices')]
    public function invoices(): Response
    {
        return $this->render('user_account/user_invoices.html.twig', [
        ]);
    }

    #[Route('/utilisateur/compte/catalog', name: 'app_user_account_catalog')]
    public function catalog(EntityManagerInterface $entityManager): Response
    {
        $video = $entityManager->getRepository(Video::class)->findAll();

        return $this->render('user_account/user_catalog.html.twig', [
            "video"=>$video,
        ]);
    }
}
