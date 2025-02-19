<?php

namespace App\Controller;

use App\Class\Mail;
use App\Entity\Catalog;
use App\Entity\Reservation;
use App\Entity\StatsEvent;
use App\Entity\StatsVideo;
use App\Repository\EventRepository;
use App\Repository\OrderDetailRepository;
use App\Repository\OrderRepository;
use App\Repository\StatsEventRepository;
use App\Repository\StatsVideoRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Null_;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;


final class PaymentController extends AbstractController
{
    #[Route('/payment/{id_order}', name: 'app_payment')]
    public function index($id_order, OrderRepository $orderRepository, EntityManagerInterface $entityManagerInterface): Response
    {

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        // dd($id_order);

        $order= $orderRepository->findOneBy([
            'id'=>$id_order,
            'user'=>$this->getUser(),
        ]);

        // dd($order);

        if(!$order){
            return $this->redirectToRoute('app_home');
        }

        $productForStripe = [];

        foreach ($order->getOrderDetails() as $product){

            // dd($product);
            $productForStripe[]= [
                'price_data' => [
                    'currency'=>'eur',
                    'unit_amount'=>number_format($product->getTotalTTC()*100, 0,'',''),
                    'product_data'=>[
                        'name'=>$product->getProductName(),
                        'images'=>[
                           $_ENV['DOMAIN'].'uploads/'.$product->getProductImage(),
                      ]
                    ]
              ],
              'quantity' => $product->getProductQuantity(),
            ];
        }

        // dd($productForStripe);


        $checkout_session = Session::create([
        'line_items' => [[
            $productForStripe
        ]],
        'mode' => 'payment',
        'success_url' => $_ENV['DOMAIN'] . 'commande/merci/{CHECKOUT_SESSION_ID}',
        'cancel_url' => $_ENV['DOMAIN'] . 'panier/annulation',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $entityManagerInterface->flush();
      
        return $this->redirect($checkout_session->url);
    }

    #[Route('/commande/merci/{stripe_session_id}', name: 'app_payment_success')]
    public function success($stripe_session_id, OrderRepository $orderRepository, EventRepository $eventRepository,StatsEventRepository $statsEventRepository, EntityManagerInterface $entityManager, SessionInterface $session, VideoRepository $videoRepository,StatsVideoRepository $statsVideoRepository, OrderDetailRepository $orderDetailRepository): Response
    {
        $user = $this->getUser();
        // dd($user);

        $mail= New Mail();
        // variables pour la template
        $vars=[
            'firstname'=>$user->getFirstName(),
            'lastname'=>$user->getLastName(),
        ];
        $mail->send($user->getEmail(),$user->getFirstName().' '.$user->getLastName(), 'Commande sur le site VOD Project','thank_you.html',$vars);

        $catalogExists=false;
        $reservationExists=false;
        $isEvent=false;

        $order=$orderRepository->findOneBy([
            'stripe_session_id'=>$stripe_session_id,
            'user'=>$this->getUser(),
        ]);

        if(!$order){
            return $this->redirectToRoute('app_home');
        }

        if($order->getStatus()==1){
            $order->setStatus(2);
            $products = $order->getOrderDetails();
            foreach($products as $product){
                $element = $product->getProductName();
                $video = $videoRepository->findOneBy([
                    'name'=>$element,
                ]);
                $positionToCut=strpos($element,"|");
                $nameToCut= substr($element,0,$positionToCut-1);
                $event = $eventRepository->findOneBy([
                    'name'=>$nameToCut,
                ]);
                // dd($nameToCut);
                // dd($event);
                if($video != NULL){
                    $videoToAdd = $video->getId();
                    $catalog = New Catalog;
                    $catalog->setUserId($order->getUser()->getId());
                    $catalog->setVideoId($videoToAdd);
                    $entityManager->persist($catalog);
                    // incrémenter le StatsVideo par rapport à l'id_video
                    // chercher dans le repository avec un find 
                    if($statsVideoRepository->findOneBy([
                        'video_id'=>$video->getId(),
                    ]) == NULL){

                        $statsVideoToIncrement= New StatsVideo;
                        $statsVideoToIncrement->setVideoId($video->getId());
                        $statsVideoToIncrement->setVideoName($video->getName());
                        $statsVideoToIncrement->setPlayCount(0);
                        $entityManager->persist($statsVideoToIncrement);
                    }else{
                        $statsVideoToIncrement=$statsVideoRepository->findOneBy([
                            'video_id'=>$video->getId(),
                        ]);
                    }
                    // dd($statsVideoToIncrement);
                    $actualVideoPlayCount=$statsVideoToIncrement->getPlayCount();
                    $statsVideoToIncrement->setPlayCount($actualVideoPlayCount+1);
                }
                if($event != NULL){
                    $eventToAdd = $event->getId();
                    $reservation = New Reservation;
                    $reservation->setUserId($order->getUser()->getId());
                    $reservation->setemail($order->getUser()->getEmail());
                    $reservation->setFirstName($order->getUser()->getFirstName());
                    $reservation->setLastName($order->getUser()->getLastName());
                    $reservation->setEventId($eventToAdd);
                    $reservation->setOrderId($order->getId());
                    $reservation->setBoughtDate((new \DateTime()));
                    $reservation->setNumberOfTickets(0);
                    $reservation->setNumberOfTicketsNoFood(0);
                    $reservation->setNumberOfTicketsWithFood(0);


                    // boolean
                    $isEvent=true;
                }
            }
            if($isEvent == true){
                // créer un stat seulement s'il n'existe pas
                if($statsEventRepository->findOneBy([
                    'event_id'=>$event->getId(),
                ]) == NULL){
                    $statsEventToIncrement= New StatsEvent;
                    $statsEventToIncrement->setEventId($event->getId());
                    $statsEventToIncrement->setEventName($event->getName());
                    $statsEventToIncrement->setPlayCount(0);
                    $statsEventToIncrement->setNoFoodStats(0);
                    $statsEventToIncrement->setWithFoodStats(0);
                }else{
                    $statsEventToIncrement=$statsEventRepository->findOneBy([
                        'event_id'=>$event->getId(),
                    ]);
                }
                $orderDetails=$orderDetailRepository->findBy([
                    'myOrder'=>$order->getId(),
                ]);
                
                
                $currentTotalEventTickets=0;
                $orderDetailsLength=count($orderDetails);
                
                for($i=0;$i<$orderDetailsLength;$i++){
                    $productQuantity=$orderDetails[$i]->getProductQuantity();
                    $currentTotalEventTickets+=$productQuantity;
                    // dd($orderDetails[$i]->getProductName());
                    if(str_contains($orderDetails[$i]->getProductName(),"Repas")){
                        // dd("formule avec foood");
                        $actualWithFoodStats=$statsEventToIncrement->getWithFoodStats();
                        $statsEventToIncrement->setWithFoodStats($productQuantity+$actualWithFoodStats);
                        // dd($statsEventToIncrement->getWithFoodStats());
                        $reservation->setNumberOfTicketsWithFood($productQuantity);
                        
                        
                    }else{
                        // dd("formule no foood");
                        $actualNoFoodStats=$statsEventToIncrement->getNoFoodStats();
                        $statsEventToIncrement->setNoFoodStats($productQuantity+$actualNoFoodStats);
                        // dd($statsEventToIncrement->getNoFoodStats());
                        $reservation->setNumberOfTicketsNoFood($productQuantity);
                    }
                };
                // set reservation totalTickets
                $reservation->setNumberOfTickets($currentTotalEventTickets);
                $actualEventPlayCount=$statsEventToIncrement->getPlayCount();
                $statsEventToIncrement->setPlayCount($actualEventPlayCount+$currentTotalEventTickets);
                $entityManager->persist($statsEventToIncrement);
                $entityManager->persist($reservation);
            }

            $session->remove('panier');
            $session->remove('panierReservationNoFood');
            $session->remove('panierReservationWithFood');
        }
        $entityManager->flush();
       
        return $this->render('payment/success.html.twig', [
            'order'=>$order
        ]);
    }
}
