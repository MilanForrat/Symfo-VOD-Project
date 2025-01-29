<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use App\Repository\VideoRepository;
use DateTime;
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
        $total=0;

        foreach($panier as $key => $quantity){
            $video = $this->videoRepository->find($key);
            $data[]= [
                "video"=>$video,
                "quantity"=>$quantity,
            ];
            $total += $video->getPrice() * $quantity;
        }

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
            'total' => $total,
        ]);
    }
}
