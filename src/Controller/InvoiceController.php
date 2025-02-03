<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class InvoiceController extends AbstractController
{
    /*
    * Impression facture PDF pour un utilisateur connecté
    * Vérification commande pour un utilisateur donné
    */
    #[Route('compte/facture/impression/{id_order}', name: 'app_invoice_client')]
    public function invoiceForClient(OrderRepository $orderRepository, $id_order): Response
    {
        // Vérification de l'objet commande
        $order = $orderRepository->findOneById($id_order);

        if(!$order){
            return $this->redirectToRoute("app_home");
        }

        // Vérification que l'utilisateur connecté correspond à l'utilisateur de la commande
        if($order->getUser() != $this->getUser()){
            return $this->redirectToRoute("app_home");
        }

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();

        $html=$this->renderView('invoice/index.html.twig', [
            "order"=>$order,
        ]);

        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('facture.pdf', [
            // permet d'ouvrir le fichier dans le pdf
            'Attachment'=>false,
        ]);

        exit();
    }

    /*
    * Impression facture PDF pour un administrateur connecté
    * Vérification commande pour un utilisateur donné
    */
    #[Route('admin/facture/impression/{id_order}', name: 'app_invoice_admin')]
    public function invoiceForAdministrator(OrderRepository $orderRepository, $id_order): Response
    {
        // Vérification de l'objet commande
        $order = $orderRepository->findOneById($id_order);

        if(!$order){
            return $this->redirectToRoute("admin");
        }

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();

        // au besoin on pourrait créer une facture spéciale pour les administrateurs
        $html=$this->renderView('invoice/index.html.twig', [
            "order"=>$order,
        ]);

        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('facture.pdf', [
            // permet d'ouvrir le fichier dans le pdf
            'Attachment'=>false,
        ]);

        exit();
    }
}
