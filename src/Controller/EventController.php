<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

final class EventController extends AbstractController
{

    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {
    }

    #[Route('/evenement', name: 'app_all_events')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $evenement = $entityManager->getRepository(Event::class)->findAll();

        return $this->render('event/index.html.twig', [
            'events' => $evenement,
        ]);
    }

    #[Route('/evenement/{id}', name: 'app_event_details')]
    public function evenementById(EntityManagerInterface $entityManager, int $id): Response
    {
        $evenement = $entityManager->getRepository(Event::class)->findOneById($id);

        if(!$evenement){
            return $this->redirectToRoute('app_home');
        }

        return $this->render('event/details.html.twig', [
            'event' => $evenement,
        ]);
    }

    // Route qui affiche le récapitulatif de la réservation
    #[Route('/evenement/reservation/{id}', name: 'app_event_reservation', defaults:['formule'=>NULL])]
    public function evenementReservation(EntityManagerInterface $entityManager,Session $session , int $id, $formule, Request $request): Response
    {
        $isEmptyNoFood=true;
        $isEmptyWithFood=true;
        $evenement = $entityManager->getRepository(Event::class)->findOneById($id);

        if(!$evenement){
            return $this->redirectToRoute('app_home');
        }

        // afficher le panier
        $panierReservationNoFood = $session->get('panierReservationNoFood', []);
        $panierReservationWithFood = $session->get('panierReservationWithFood', []);

        // dd($panierReservationNoFood[$id]);

        $data=[];
        $totalPriceEventWithFood=0;
        $totalPriceEventNoFood=0;
        $totalPriceReservations=0;

        $totalNumberOfEventWithFood=0;
        $totalNumberOfEventNoFood=0;
        $totalNumberOfReservations=0;

        if(array_key_exists($id,$panierReservationNoFood)){
            $isEmptyNoFood=false;

            $quantityNoFood=$panierReservationNoFood[$id];

            $panierReservationNoFood=$this->eventRepository->find($id);

            // dd($quantityWithFood);
            $data[]= [
                            
                "panierReservationNoFood"=>$panierReservationNoFood,
                "quantity"=>$quantityNoFood,
            ];

            $totalPriceEventNoFood += $panierReservationNoFood->getEventPriceNoFood()*$quantityNoFood;
            $totalNumberOfEventNoFood= $quantityNoFood;

            // dd($totalEventNoFood);

            $totalPriceReservations+=$totalPriceEventNoFood;
            $totalNumberOfReservations+=$totalNumberOfEventNoFood;

        }
        if(array_key_exists($id,$panierReservationWithFood)){
            $isEmptyWithFood=false;
            $quantityWithFood=$panierReservationWithFood[$id];

            $panierReservationWithFood=$this->eventRepository->find($id);

            $panierReservationWithFood=$this->eventRepository->find($id);
            $data[]= [
                
                "panierReservationWithFood"=>$panierReservationWithFood,
                "quantity"=>$quantityWithFood,
            ];

            $totalPriceEventWithFood += $panierReservationWithFood->getEventPriceWithFood()*$quantityWithFood;
            $totalNumberOfEventWithFood= $quantityWithFood;

            // dd($totalEventWithFood);

            $totalPriceReservations+=$totalPriceEventWithFood;
            $totalNumberOfReservations+=$totalNumberOfEventWithFood;
        }

        return $this->render('event/reservation_events.html.twig', [
            'event' => $evenement,
            'data' => $data,
            'totalPriceEventNoFood' => $totalPriceEventNoFood,
            'totalPriceEventWithFood'=>$totalPriceEventWithFood,
            'isEmptyNoFood'=>$isEmptyNoFood,
            'isEmptyWithFood'=>$isEmptyWithFood,
            'totalPriceReservations'=>$totalPriceReservations,
            'totalNumberOfEventNoFood'=>$totalNumberOfEventNoFood,
            'totalNumberOfEventWithFood'=>$totalNumberOfEventWithFood,
            'totalNumberOfReservations'=>$totalNumberOfReservations,
        ]);
    }

    #[Route('/evenement/ajouter/reservation/formule-1/{id}', name: 'app_add_reservation_no_food')]
    public function addReservationNoFoodPanier($id,SessionInterface $session, Request $request): Response
    {
        $panierReservationNoFood = $session->get('panierReservationNoFood', []);

        if(empty($panierReservationNoFood[$id])){
            $panierReservationNoFood[$id]=1;
        }else{
            $panierReservationNoFood[$id]++;
        }
        
        $session->set('panierReservationNoFood', $panierReservationNoFood);
        // dd($panierReservationNoFood);

        $this->addFlash(
            'success',
            'Formule Pass\'Evènement ajoutée au panier'
        );

        return $this->redirect($request->headers->get('referer'));
    }
    #[Route('/evenement/ajouter/reservation/formule-2/{id}', name: 'app_add_reservation_with_food')]
    public function addReservationWithFoodPanier($id,SessionInterface $session, Request $request): Response
    {

        $panierReservationWithFood = $session->get('panierReservationWithFood', []);

        if(empty($panierReservationWithFood[$id])){

            $panierReservationWithFood[$id]=1;
        }else{
            $panierReservationWithFood[$id]++;
        }
        

        $session->set('panierReservationWithFood', $panierReservationWithFood);
        // dd($panierReservationWithFood);

        $this->addFlash(
            'success',
            'Formule Pass\'Evènement + Repas ajoutée au panier'
        );
   
        return $this->redirect($request->headers->get('referer'));
    }

    // créer route pour enlever 1 ticket
    #[Route('/evenement/supprimer/reservation/formule-2/{id}', name: 'app_delete_reservation_with_food')]
    public function deleteReservationWithFoodPanier($id,SessionInterface $session, Request $request): Response
    {
        $panierReservationWithFood = $session->get('panierReservationWithFood', []);

        if(!empty($panierReservationWithFood[$id])){
            // si l'élément du panier à une QT > 1 on décrémente sa QT 
            if($panierReservationWithFood[$id]>1){
                $panierReservationWithFood[$id]--;
                // dd($panier[$id]);
            }else{
            // sinon on l'enlève du tableau, ca supprime la variable de la $session
            unset($panierReservationWithFood[$id]);
            }
        }

        $session->set('panierReservationWithFood', $panierReservationWithFood);

        $this->addFlash(
            'success',
            'Formule Pass\'Evènement + Repas retiré du panier'
        );

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/evenement/supprimer/reservation/formule-1/{id}', name: 'app_delete_reservation_no_food')]
    public function deleteReservationNoFoodPanier($id,SessionInterface $session, Request $request): Response
    {

        $panierReservationNoFood = $session->get('panierReservationNoFood', []);

        if(!empty($panierReservationNoFood[$id])){
            // si l'élément du panier à une QT > 1 on décrémente sa QT 
            if($panierReservationNoFood[$id]>1){
                $panierReservationNoFood[$id]--;
                // dd($panier[$id]);
            }else{
            // sinon on l'enlève du tableau, ca supprime la variable de la $session
            unset($panierReservationNoFood[$id]);
            }
        }

        $session->set('panierReservationNoFood', $panierReservationNoFood);

        $this->addFlash(
            'success',
            'Formule Pass\'Evènement retiré du panier'
        );

        return $this->redirect($request->headers->get('referer'));


    }

    // créer fonction dans le panier pour afficher les évènements 


}
