<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use App\Repository\EventRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    public function __construct(
        private readonly VideoRepository $videoRepository,
        private readonly EventRepository $eventRepository,
    ) {
    }

    #[Route('/commande/adresse', name: 'app_order')]
    public function index(): Response
    {
        $addresses = $this->getUser()->getAddresses();

        if(count($addresses)==0){
            return $this->redirectToRoute('app_user_account_address_form');
        }


        $form=$this->createForm(OrderType::class, null, [
            'addresses'=>$addresses,
            'action'=>$this->generateUrl('app_recap'),
        ]);

        return $this->render('order/index.html.twig', [
            'facturationForm' => $form->createView(),
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'app_recap')]
    public function recapAndAddInDataBase(Request $request, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        if($request->getMethod()!= 'POST'){
            return $this->redirectToRoute('app_panier');
        }

        $panier = $session->get('panier',[]);
        $data=[];

        $totalVideoHT=0;
        $totalVideoTTC=0;
        $totalVideoTVA=0;

        $totalHT=0;
        $totalTTC=0;
        $totalTVA=0;

        foreach($panier as $key => $quantity){
            $video = $this->videoRepository->find($key);
            $data[]= [
                "video"=>$video,
                "quantity"=>$quantity,
            ];
            $totalVideoHT += $video->getPrice() * $quantity;
            $totalVideoTTC += $video->getVideoTTC()*$quantity;
            $totalVideoTVA += $video->getPriceTvaCalculator()*$quantity;
        }

        // ----------- TICKETS ---------
        $panierReservationNoFood = $session->get('panierReservationNoFood', []);
        $panierReservationWithFood = $session->get('panierReservationWithFood', []);

        $dataEventNoFood=[];
        $dataEventWithFood=[];

        $totalPriceEventWithFood=0;
        $totalPriceEventNoFood=0;
        $totalPriceReservations=0;

        $totalNumberOfEventWithFood=0;
        $totalNumberOfEventNoFood=0;
        $totalNumberOfReservations=0;

        foreach($panierReservationNoFood as $key => $quantity){

            $quantityNoFood=$panierReservationNoFood[$key];

            $eventReservationNoFood=$this->eventRepository->find($key);

            // dd($eventReservationNoFood);

            $dataEventNoFood[]= [
                "panierReservationNoFood"=>$eventReservationNoFood,
                "quantity"=>$quantityNoFood,
            ];
         
            $eventReservationNoFood->getEventPriceNoFood()*$quantityNoFood;
            $totalNumberOfEventNoFood= $quantityNoFood;

            $totalPriceEventNoFood += $eventReservationNoFood->getEventPriceNoFood()*$quantityNoFood;
            $totalNumberOfEventNoFood= $quantityNoFood;

            $totalPriceReservations+=$totalPriceEventNoFood;
            $totalNumberOfReservations+=$totalNumberOfEventNoFood;
        }

        foreach($panierReservationWithFood as $key => $quantity){

            $quantityWithFood=$panierReservationWithFood[$key];

            $eventReservationWithFood=$this->eventRepository->find($key);

            $dataEventWithFood[]= [
                "panierReservationWithFood"=>$eventReservationWithFood,
                "quantity"=>$quantityWithFood,
            ];
         
            $eventReservationWithFood->getEventPriceWithFood()*$quantityWithFood;
            $totalNumberOfEventWithFood= $quantityWithFood;

            $totalPriceEventWithFood += $eventReservationWithFood->getEventPriceWithFood()*$quantityWithFood;
            $totalNumberOfEventWithFood= $quantityWithFood;

            $totalPriceReservations+=$totalPriceEventWithFood;
            $totalNumberOfReservations+=$totalNumberOfEventWithFood;
        }

        // ---------- FIN TICKETS --------

        // ------------ CALCULS TOTAUX $ ----------
        $totalEventsHT = $totalPriceReservations/1.2;
        $totalEventsTTC= $totalPriceReservations;
        $totalEventsTVA = $totalPriceReservations*0.2;
        
        $totalHT=$totalVideoHT+$totalEventsHT;
        $totalTTC=$totalVideoTTC+$totalEventsTTC;
        $totalTVA=$totalVideoTVA+$totalEventsTVA;

        $form=$this->createForm(OrderType::class, null, [
            'addresses'=>$this->getUser()->getAddresses(),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // création chaine de caractère Adresse
            $addressObject=$form->get('addresses')->getData();

            $address= $addressObject->getEnterprise().'<br>';
            $address .= $addressObject->getFirstName().' '.$addressObject->getLastName().'<br>';
            $address.= $addressObject->getPhone().'<br>';
            $address.= $addressObject->getAddress().'<br>';
            $address.= $addressObject->getCity().' '.$addressObject->getPostCode().'<br>';
            $address.= $addressObject->getCountry();

            $order = new Order;
            $order->setUser($this->getUser());
            $order->setCreatedAt(new \DateTime());
            $order->setStatus(1);
            $order->setAddressFacturation($address);

            foreach($data as $product){
                // dd($product['quantity']);
                $orderDetail= New OrderDetail();
                $orderDetail->setProductName($product['video']->getName());
                $orderDetail->setProductImage($product['video']->getImage());
                $orderDetail->setProductQuantity($product['quantity']);
                $orderDetail->setProductPrice($product['video']->getPrice());
                $orderDetail->setProductTVA($product['video']->getTva());
                // dd($product['video']->getTva());

                // on récupère le contenu du panier et on le greffe à orderDetail
                $order->addOrderDetail($orderDetail);
            }
            foreach($dataEventNoFood as $eventNoFood){
                $orderDetail= New OrderDetail();
                $orderDetail->setProductName($eventNoFood['panierReservationNoFood']->getName().' | Formule Pass\'Evènement');
                $orderDetail->setProductImage($eventNoFood['panierReservationNoFood']->getImage());
                // dd($eventNoFood['panierReservationNoFood']->getImage());
                $orderDetail->setProductQuantity($eventNoFood['quantity']);
                $orderDetail->setProductPrice($eventNoFood['panierReservationNoFood']->getEventPriceNoFoodHT());
                // dd($eventNoFood['panierReservationNoFood']->getEventPriceNoFoodHT());
                $orderDetail->setProductTVA(20.0);

                // on récupère le contenu du panier et on le greffe à orderDetail
                $order->addOrderDetail($orderDetail);
            }
            foreach($dataEventWithFood as $eventWithFood){
                // dd($dataEventWithFood);
                $orderDetail= New OrderDetail();
                $orderDetail->setProductName($eventWithFood['panierReservationWithFood']->getName().' | Formule Pass\'Evènement + Repas');
                $orderDetail->setProductImage($eventWithFood['panierReservationWithFood']->getImage());
                $orderDetail->setProductQuantity($eventWithFood['quantity']);
                $orderDetail->setProductPrice($eventWithFood['panierReservationWithFood']->getEventPriceWithFoodHT());
                $orderDetail->setProductTVA(20.0);

                // on récupère le contenu du panier et on le greffe à orderDetail
                $order->addOrderDetail($orderDetail);
            }
            
            // on envoie en BDD
            $entityManager->persist($order);
            $entityManager->flush();

        }

        return $this->render('order/recap.html.twig', [
            'address' => $form->getData(),
            'data' => $data,
            'dataEventNoFood'=>$dataEventNoFood,
            'dataEventWithFood'=>$dataEventWithFood,
            'totalHT' => $totalHT,
            'totalTTC' => $totalTTC,
            'totalTVA' => $totalTVA,
            'order'=>$order,
            'totalPriceEventNoFood' => $totalPriceEventNoFood,
            'totalPriceEventWithFood'=>$totalPriceEventWithFood,
            'totalPriceReservations'=>$totalPriceReservations,
            'totalNumberOfEventNoFood'=>$totalNumberOfEventNoFood,
            'totalNumberOfEventWithFood'=>$totalNumberOfEventWithFood,
            'totalNumberOfReservations'=>$totalNumberOfReservations,
        ]);
    }
}
