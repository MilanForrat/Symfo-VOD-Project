<?php

namespace App\Services;

use Stripe\Stripe;
use App\Services\StripeServiceInterface;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeService implements StripeServiceInterface{

    // stripe va nous renvoyer des éléments concernant les paiements et ces variables permettront de les stockées dans notre BDD une fois récupérées
    private const STRIPE_PAYMENT_ID = "session_stipe_payment_id";
    private const STRIPE_PAYMENT_ORDER = "session_stripe_payment_order";

    // puis RDV sur Packagist et récupérer : composer require stripe/stripe-php

    // puis dans config/services.yaml ajouter aux parameters : stripeSecret: '%env(STRIPE_SECRET)%'

    public function __construct(
        private readonly string $stripeSecret,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly RequestStack $requestStack,
        )
    {
        Stripe::SetApiKey($stripeSecret);
    }

    public function payment($panier, $id_order):string{
        $mySession =$this->requestStack->getSession();
        $session = Session::create([
            'success_url'=>$this->urlGenerator->generate('app_stripe_success',['order'=>$id_order->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url'=>$this->urlGenerator->generate('app_stripe_cancel',['order'=>$id_order->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'payment_method_types'=>['card'],
            'line_items'=>[
                    [
                        $this->getLinesItems($panier),
                    ]
                ],
            'mode'=>'payment',
        ]);

        $mySession->set(self::STRIPE_PAYMENT_ID, $session->id);
        $mySession->set(self::STRIPE_PAYMENT_ORDER, $id_order->getId());

        return $session->url;
    }

    public function getSessionId():mixed{
        return $this->requestStack->getSession()->get(self::STRIPE_PAYMENT_ID);
    }

    public function getSessionOrder():mixed{
        return $this->requestStack->getSession()->get(self::STRIPE_PAYMENT_ORDER);
    }

    private function getLinesItems($panier):array{
        $produtc = [];
        foreach($panier as $item){
            $product['price_data']['currency']="eur";
            $product['price_data']['product_data']['name'] = $item["video"]->getName();
            $product['price_data']['unit_amount'] = $item["video"]->getPrice()*100;
            $product['quantity']=$item['quantity'];
            
            $products[]= $product;
        }
        return $products;
    }
}

