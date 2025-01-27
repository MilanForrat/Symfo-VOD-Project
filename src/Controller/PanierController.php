<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\VideoRepository;

// pour la session
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierController extends AbstractController
{

    // nécessaire pour la suite du panier
    public function __construct(
        private readonly VideoRepository $videoRepository,
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

        if(empty($panier)){
            $isEmpty=true;
            // dd($isEmpty);
        }else{
            $isEmpty=false;
            // dd($isEmpty);
        }

        // pour débugger
        // dd($session);

        // on initialise des variables qui vont nous permettre de travailler sur les calculs et boucles du panier
        // data sera le tableau de tous les produits/videos...
        // total sera le prix total 
        $data=[];
        $total=0;

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
            $total += $video->getPrice() * $quantity;
        }

        // pour mieux comprendre
        // dd($data);
        // dd($total);

        // on envoie les variables à la template pour accéder à ces dernières
        return $this->render('panier/index.html.twig', [
            'data' => $data,
            'total' => $total,
            "isEmpty" =>$isEmpty
        ]);
    }

    // Route qui gère l'ajout au panier (dans la session)
    #[Route('/panier/add/{id}', name: 'app_add_panier')]
    public function add($id,SessionInterface $session): Response
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

        // voir le contenu de la session
        // dd($session);

        // ici on redirige vers le panier 
        return $this->redirectToRoute("app_panier",array('motif' => "ajout"));
    }

    // route qui gère la suppression d'un élément au panier
    #[Route('/panier/delete/{id}', name: 'app_delete_panier')]
    public function delete($id,SessionInterface $session): Response
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
        return $this->redirectToRoute("app_panier",array('motif' => "suppression"));
    }
}
