<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\VideoRepository;
use Symfony\Component\HttpFoundation\Request;
// pour la session
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierController extends AbstractController
{

    // nécessaire pour la suite du panier
    public function __construct(
        private readonly VideoRepository $videoRepository,
        private readonly EventRepository $eventRepository,
    ) {
    }

    #[Route('/panier/{motif}', name: 'app_panier', defaults:['motif'=>NULL])]
    public function index(SessionInterface $session, $motif): Response
    {

        if($motif =="annulation"){
            $this->addFlash(
                type:'info',
                message:'Paiement annulé. Vous pouvez mettre à jour votre panier et votre commande.'
            );
        }
        if($motif =="suppression"){
            $this->addFlash(
                type:'info',
                message:'Article retiré du panier.'
            );
        }
        if($motif =="ajout"){
            $this->addFlash(
                type:'success',
                message:'Article ajouté au panier.'
            );
        }

        // check user connected
        if($user = $this->getUser()){
        }else{
            return $this->redirectToRoute("app_login");
        }

        // on récupère le panier de la session 
        $panier = $session->get('panier',[]);
        $panierReservationNoFood = $session->get('panierReservationNoFood', []);
        $panierReservationWithFood = $session->get('panierReservationWithFood', []);

        // dd($panier);
        if(!empty($panier) || !empty($panierReservationNoFood) || !empty($panierReservationWithFood)){
            $isEmpty=false;
            // dd($isEmpty);
        }else{
            $isEmpty=true;
            // dd($isEmpty);
        }

        // pour débugger
        // dd($session);

        // on initialise des variables qui vont nous permettre de travailler sur les calculs et boucles du panier
        // data sera le tableau de tous les produits/videos...
        // total sera le prix total 
        $data=[];
        $totalVideoHT=0;
        $totalVideoTTC=0;
        $totalVideoTVA=0;

        $totalHT=0;
        $totalTTC=0;
        $totalTVA=0;

        // boucle des produits/videos... du panier de la session
        // pour chaque clé du panier en face tu as une Quantité
        // on parcourt le tableau panier (voir dd($session) si besoin)
       
        foreach($panier as $key => $quantity){
            // je récupère les infos de chaque video/product... en fonction de la clé (qui est l'id)
            $video = $this->videoRepository->find($key);

            // pour mieux comprendre
            // dd($video);
            // dans notre tableau data, on va avoir un sous-tableau de produit => quantity
            $data[]= [
                "video"=>$video,
                "quantity"=>$quantity,
            ];
         
            $totalVideoHT += $video->getPrice() * $quantity;
            $totalVideoTTC += $video->getVideoTTC()*$quantity;
            $totalVideoTVA += $video->getPriceTvaCalculator()*$quantity;
        }

        // ------------ TICKETS -----------
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


        // ------------ FIN TICKETS ----------

        // ------------ CALCULS TOTAUX $ ----------
        $totalEventsHT = $totalPriceReservations/1.2;
        $totalEventsTTC= $totalPriceReservations;
        $totalEventsTVA = $totalPriceReservations*0.2;

        // dd($totalEventsTVA);

        $totalHT=$totalVideoHT+$totalEventsHT;
        $totalTTC=$totalVideoTTC+$totalEventsTTC;
        $totalTVA=$totalVideoTVA+$totalEventsTVA;

        return $this->render('panier/index.html.twig', [
            'data' => $data,
            'dataEventNoFood'=>$dataEventNoFood,
            'dataEventWithFood'=>$dataEventWithFood,
            'totalHT' => $totalHT,
            'totalTTC' => $totalTTC,
            'totalTVA' => $totalTVA,
            "isEmpty" =>$isEmpty,
            'totalPriceEventNoFood' => $totalPriceEventNoFood,
            'totalPriceEventWithFood'=>$totalPriceEventWithFood,
            'totalPriceReservations'=>$totalPriceReservations,
            'totalNumberOfEventNoFood'=>$totalNumberOfEventNoFood,
            'totalNumberOfEventWithFood'=>$totalNumberOfEventWithFood,
            'totalNumberOfReservations'=>$totalNumberOfReservations,
        ]);
    }

    // Route qui gère l'ajout au panier (dans la session)
    #[Route('/panier/ajouter/{id}', name: 'app_add_panier')]
    public function add($id,SessionInterface $session, Request $request): Response
    {
        // on donne à la session la variable panier comme un tableau vide
        $panier = $session->get('panier', []);

        // si le panier est vide
        if(empty($panier[$id])){
            // on attribue à une variable qu'on a nommé panier, l'id du produit/video/article... cliqué et la quantité 1
            $panier[$id]=1;
        }else{
            // sinon on ajoute 1 à la quantité du panier 
            // ATTENTION, si l'on ne veut pas que la quantité soit > 1 alors pas d'incrémentation (ex: achat d'une vidéo VOD)
            // $panier[$id]++;
        }
        
        // on envoie la variable panier à la session pour récupérer son contenu plus tard
        $session->set('panier', $panier);

        $this->addFlash(
            'success',
            'Produit ajouté au panier'
        );
        // voir le contenu de la session
        // dd($session);

        // ici on redirige vers le panier 
        // return $this->redirectToRoute("app_panier",array('motif' => "ajout"));

        // redirige l'utilisateur vers la dernière page visitée
        return $this->redirect($request->headers->get('referer'));
    }

    // route qui gère la suppression d'un élément au panier
    #[Route('/panier/supprimer/{id}', name: 'app_delete_panier')]
    public function delete($id,SessionInterface $session, Request $request): Response
    {

        // on donne à la session la variable panier comme un tableau vide
        $panier = $session->get('panier', []);
        // dd($panier);
        // on s'assure que le panier ne soit PAS vide
        if(!empty($panier[$id])){
            // si l'élément du panier à une QT > 1 on décrémente sa QT 
            if($panier[$id]>1){
                $panier[$id]--;
                // dd($panier[$id]);
            }else{
            // sinon on l'enlève du tableau, ca supprime la variable de la $session
            unset($panier[$id]);
            }
        }
        
        // on envoie la variable panier à la session pour récupérer son contenu plus tard
        $session->set('panier', $panier);

        // voir le contenu de la session
        // dd($session);

        // ici on redirige vers le panier 
        // return $this->redirectToRoute("app_panier",array('motif' => "suppression"));

        $this->addFlash(
            'success',
            'Produit retiré du panier'
        );


        // redirige l'utilisateur vers la dernière page visitée
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/panier/vide', name: 'app_empty_panier')]
    public function trash(SessionInterface $session): Response
    {
        dd($session);
        $session->remove('panier');

        return $this->redirectToRoute("app_panier");
    }

}
