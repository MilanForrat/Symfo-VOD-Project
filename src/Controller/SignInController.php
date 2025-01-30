<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignInType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SignInController extends AbstractController
{
    #[Route('/sign-in', name: 'app_sign_in')]
    // on ajoute "Request" pour injecter une requête SQL afin d'envoyer les données saisies sur le form en BDD
    public function index(Request $req, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {

        // on va créer un nouvel utilisateur via l'objet User (ne pas oublier le "use")
        $user= new User();

        // on génère un formulaire de création de compte (celui situé dans le dossier Form), et on l'associe au nouvel utilisateur (on oublie pas le "use")
        $form = $this->createForm(SignInType::class, $user);

        // gère le fait que le formulaire a été soumis
        $form->handleRequest($req);

        // on vérifie que le formulaire a bien été soumis et validé
        if($form->isSubmitted() && $form->isValid()){
            // on attribue les données du formulaire au nouvel utilisateur
            $user=$form->getData();

            // Hashage du mot de passe
            // on récupère le mot de passe en clair
            $plaintextPassword = $user->getPassword();

            // le mot de passe et le user sont passés en paramètre du UserPasswordHasher
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            // maintenant que le mot de passe hashé est prêt on attribue le mot de passe hashé au user
            $user->setPassword($hashedPassword);


            
            $entityManager->persist($user);
            // on envoie en BDD
            $entityManager->flush();

            // afficher message succès 
            $this->addFlash('success',"Compte créer avec succès, vérifiez vos e-mail pour complèter l'inscription.");
        }else{
            // afficher message erreur
            $this->addFlash('danger',"Une erreur est survenue lors de la création du compte.");
        }

        return $this->render('sign_in/index.html.twig', [
            // on demande que le formulaire soit envoyé à la vue
            'form' => $form->createView(),
        ]);
    }
}
