<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function success($stripe_session_id, OrderRepository $orderRepository, EntityManagerInterface $entityManagerInterface, SessionInterface $session): Response
    {
        $order=$orderRepository->findOneBy([
            'stripe_session_id'=>$stripe_session_id,
            'user'=>$this->getUser(),
        ]);

        if(!$order){
            return $this->redirectToRoute('app_home');
        }

        if($order->getStatus()==1){
            $order->setStatus(2);
            $session->remove('panier');
        }
        $entityManagerInterface->flush();
        // dd($order);



        return $this->render('payment/success.html.twig', [
            'order'=>$order
        ]);
    }
}
