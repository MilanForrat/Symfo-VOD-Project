<?php

namespace App\Controller;

// permet de gérer l'autentification des utilisateurs
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $auth): Response
    {

        // on vérifie s'il y a une erreur (exemple : mot de passe éronné)
        $error = $auth->getLastAuthenticationError();

        // on récupère son adresse mail 
        $lastUsername = $auth->getLastUsername();

        return $this->render('login/index.html.twig', [
            // on passe à notre vue les paramètres qui pourraient nous être utiles
            'error' => $error,
            'last_username'=>$lastUsername,
        ]);
    }
}
