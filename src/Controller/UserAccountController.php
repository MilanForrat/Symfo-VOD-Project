<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Video;
use App\Entity\Article;
use App\Repository\AddressRepository;
use App\Form\AddressUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class UserAccountController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/utilisateur/compte/informations', name: 'app_user_account')]
    public function index(): Response
    {
        return $this->render('user_account/index.html.twig', [
        ]);
    }

    #[Route('/utilisateur/compte/commandes', name: 'app_user_account_invoices')]
    public function invoices(): Response
    {
        return $this->render('user_account/user_invoices.html.twig', [
        ]);
    }

    #[Route('/utilisateur/compte/catalogue', name: 'app_user_account_catalog')]
    public function catalog(): Response
    {
        $video = $this->entityManager->getRepository(Video::class)->findAll();

        return $this->render('user_account/user_catalog.html.twig', [
            "video"=>$video,
        ]);
    }

    #[Route('/utilisateur/compte/adresses', name: 'app_user_account_addresses')]
    public function addresses(): Response
    {
        return $this->render('user_account/addresses.html.twig', [
        ]);
    }

    #[Route('/utilisateur/compte/adresse/ajouter/{id}', name: 'app_user_account_address_form', defaults:['id'=>NULL])]
    public function addressForm(Request $request, $id, AddressRepository $addressRepository): Response
    {
        // si l'adresse existe
        if($id){
            $address=$addressRepository->findOneById($id);

            // on vérifie que l'adresse appartient à l'utilisateur en cours pour éviter qu'il essaie de voir des adresses de d'autres utilisateurs via l'url en changeant la valeur de l'id
            if(!$address OR $address->getuser() != $this->getUser()){
                return $this->redirectToRoute('app_user_account_addresses');
            }
        }
        // si l'adresse n'existe pas 
        else{
            $address=new Address();

            $address->setUser($this->getUser());
        }

        

        $form=$this->createForm(AddressUserType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($address);
            $this->entityManager->flush();

            $this->addFlash(
                'success',
                'Votre adresse est correctement enregistrée.'
            );

            return $this->redirectToRoute('app_user_account_addresses');
        }

        return $this->render('user_account/addressForm.html.twig', [
            'addressForm'=>$form,
        ]);
    }

    #[Route('/utilisateur/compte/adresse/supprimer/{id}', name: 'app_user_account_address_delete')]
    public function addressDelete($id, AddressRepository $addressRepository): Response
    {
        $address=$addressRepository->findOneById($id);

        // on vérifie que l'adresse appartient à l'utilisateur en cours pour éviter qu'il essaie de voir des adresses de d'autres utilisateurs via l'url en changeant la valeur de l'id
        if(!$address OR $address->getuser() != $this->getUser()){
            return $this->redirectToRoute('app_user_account_addresses');
        }
        $this->addFlash(
            'success',
            'Votre adresse est correctement supprimée.'
        );

        $this->entityManager->remove($address);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_user_account_addresses');
    }

}
