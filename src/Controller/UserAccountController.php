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
    public function reservations(ReservationRepository $reservationRepository,EventRepository $eventRepository, OrderDetailRepository $orderDetailRepository): Response
    {
        $eventsById=[];
        $eventsObject=[];
        $reservations = $reservationRepository->findBy([
            'user_id'=>$this->getUser()->getId(),
        ]);

        foreach($reservations as $reservation){
            // dd($reservation->getOrderId());
            $order[]=$reservation->getOrderId();
            $event = $reservation->getEventId();
            $eventsById[]=$event;
            
        }
        // allows to see only 1 event from the same event_id (prevents duplicates)
        $unique_reservations=array_unique($eventsById);

        $uniqueOrders=array_unique($order);
        // dd($unique_orders);
        $orders=[];
        // dd($uniqueOrders);
        array_push($orders, $uniqueOrders);
        $ordersReIndexed = array_map('array_values',$orders);
        // dd($ordersReIndexed);

        foreach($unique_reservations as $eventObject){
            $eventsObject[] = $eventRepository->findById($eventObject);
            
        }
        
        // récupérer tous les orders id
        // récupérer que les orders id différents 


        
        return $this->render('user_account/user_reservation.html.twig', [
            'reservations'=>$reservations,
            'eventsObject'=>$eventsObject,
            'ordersReIndexed'=>$ordersReIndexed,
        ]);
    }

}
