<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Video;
use App\Entity\Order;
use App\Entity\User;

use App\Repository\AddressRepository;
use App\Form\AddressUserType;
use App\Form\PasswordUserType;
use App\Repository\CatalogRepository;
use App\Repository\EventRepository;
use App\Repository\OrderDetailRepository;
use App\Repository\OrderRepository;
use App\Repository\ReservationRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
    public function invoices(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy([
            'user'=>$this->getUser(),
            'status'=>[2]
        ]);

        // dd($orders);
        return $this->render('user_account/user_invoices.html.twig', [
            'orders'=>$orders,
        ]);
    }

    
    #[Route('/utilisateur/compte/commandes/{id_order}', name: 'app_user_account_invoice_id')]
    public function invoiceById($id_order, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findOneBy([
            'id'=>$id_order,
            'user'=>$this->getUser()
        ]);

        if(!$order){
            return $this->redirectToRoute('app_home');
        }

        // dd($orders);
        return $this->render('user_account/invoice_by_id.html.twig', [
            'order'=>$order,
        ]);
    }

    #[Route('/utilisateur/compte/catalogue', name: 'app_user_account_catalog')]
    public function catalog(CatalogRepository $catalogRepository,VideoRepository $videoRepository): Response
    {
        $videosById=[];
        $videosObjects=[];
        $catalogs = $catalogRepository->findBy([
            'user_id'=>$this->getUser()->getId(),
        ]);

        // rajouter if pas de videos

        // pour chaque objet catalogue, je récupère la clé "video_id"
        foreach($catalogs as $catalog){
            $video = $catalog->getVideoId();
            $videosById[]=$video;
        }
        // pour chaque video_id je cherche la video correspondante 
        foreach($videosById as $videoObject){
            $videosObjects[] = $videoRepository->findById($videoObject);
        }
        // dd($videosObjects);
        
        return $this->render('user_account/user_catalog.html.twig', [
            // on passe l'objet en entier afin d'accéder à tous ses détails
            'catalogs'=>$catalogs,
            'videosObject'=>$videosObjects,
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

    #[Route('/utilisateur/compte/informations/modifier/mot-de-passe', name: 'app_user_account_password')]
    public function modifyPassword(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, EntityManagerInterface $entityManagerInterface): Response
    {
        $user= $this->getUser();

        $form=$this->createForm(PasswordUserType::class, $user, [
            'passwordHasher'=>$userPasswordHasherInterface,
        ]);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $entityManagerInterface->flush();
            $this->addFlash(
                'success',
                'Votre nouveau mot de passe est enregistré.'
            );
        }

        return $this->render('user_account/password.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    #[Route('/utilisateur/compte/reservations', name: 'app_user_account_reservations')]
    public function reservations(ReservationRepository $reservationRepository,EventRepository $eventRepository, OrderRepository $orderRepository): Response
    {
        // renvoie les reservations liées à l'utilisateur
        $reservations = $reservationRepository->findBy([
            'user_id'=>$this->getUser()->getId(),
        ]);
        $eventOrders=[];
        $eventDetails=[];
        $orderIdOfEvents=[];

        // permet de récupérer l'order Id par Evenement de l'utilisateur
        foreach($reservations as $event){
            
            // récupérer les détails de l'évènement par son id
            $eventId=$event->getEventId();

            // je recupère l'order_id de l'évènement
            $eventOrder=$event->getOrderId();

            // si la valeur existe dans le tableau alors je loop la suite, sinon rien
            // renvoie vrai si valeur trouvée dans le tableau 
            if(in_array($eventOrder, $orderIdOfEvents)){
                // dump("true");
                continue;
            }else{
                // dump("false");
                $eventOrders[]=$orderRepository->findOneById($eventOrder);
                $eventDetails[] = $eventRepository->findOneById($eventId);
                
            }
            // je stock l'order id de chaque évènement
            $orderIdOfEvents[]=$eventOrder;
            // dd($eventDetails);
        }
        
        return $this->render('user_account/user_reservation.html.twig', [
            'reservations'=>$reservations,
            'eventDetails'=>$eventDetails,
            'eventOrders'=>$eventOrders,
        ]);
    }

}
