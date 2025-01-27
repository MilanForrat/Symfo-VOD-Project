<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Services\StripeServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Repository\VideoRepository;
use App\Repository\OrderRepository;
use App\Entity\Order;
use App\Controller\SessionInterface;


class StripeController extends AbstractController
{
    public function __construct(
        private readonly StripeServiceInterface $stripeServiceInterface,
        private readonly EntityManagerInterface $entityManagerInterface,
        private readonly VideoRepository $videoRepository,
        private readonly OrderRepository $orderRepository,
        ){
    }

    #[Route('/stripe', name: 'app_stripe', methods:['GET'])]
    public function index(Session $session): Response
    {

        // on récupère le panier de la session 
        $panier = $session->get('panier',[]);

        if(empty($panier)){
            $isEmpty=true;
        }else{
            $isEmpty=false;
        }

        $data=[];
        $total=0;

        $order = new Order();
        $this->entityManagerInterface->persist($order);

        foreach($panier as $key => $quantity){
            $video = $this->videoRepository->find($key);
            $data[]= [
                "video"=>$video,
                "quantity"=>$quantity,
            ];
            $total += $video->getPrice() * $quantity;
        }

        $order->setAmountTotal($total);
        $order->setPaid(false);
        
        $order->setPaymentId(1);
        // si utilisateur connecté alors
        if($this->getUser()){
            // on affecte l'order à l'utilisateur
            $order->setUser($this->getUser());
        }

        $this->entityManagerInterface->flush();

        $url=$this->stripeServiceInterface->payment($data, $order);

        return $this->redirect($url, Response::HTTP_SEE_OTHER);
    }

    #[Route('/stripe/success/{order}', name: 'app_stripe_success')]
    public function success($order, Session $session)
    {
        $order = $this->orderRepository->find($order);

        $order->setPaid(true);

        $this->entityManagerInterface->flush();

        // si commande valider, on vide le panier
        $session->remove('panier');

        return $this->render('stripe/index.html.twig', [
        ]);
    }
    #[Route('/stripe/cancel/{order}', name: 'app_stripe_cancel')]
    public function cancel($order)
    {
        $order=$this->orderRepository->find($order);
        $this->entityManagerInterface->remove($order);
        $this->entityManagerInterface->flush();

        return $this->redirectToRoute('app_panier',array('motif' => "annulation"));
    }
}
