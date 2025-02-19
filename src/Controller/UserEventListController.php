<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\ReservationRepository;
use App\Repository\StatsEventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserEventListController extends AbstractController
{
/*
    * Impression facture PDF pour un administrateur connecté
    * Vérification commande pour un utilisateur donné
    */
    #[Route('admin/liste-evenement/impression/{event_id}', name: 'app_event_list_admin')]
    public function invoiceForAdministrator(ReservationRepository $reservationRepository, $event_id, StatsEventRepository $statsEventRepository, EventRepository $eventRepository): Response
    {
        $reservations = $reservationRepository->findByEventId($event_id);
        $event=$eventRepository->findOneById($event_id);
        $statsEvent=$statsEventRepository->findBy([
            'event_id'=>$event_id,
        ]);

        if(!$reservations){
            return $this->redirectToRoute("admin");
        }

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();

        // au besoin on pourrait créer une facture spéciale pour les administrateurs
        $html=$this->renderView('admin/pdf_event_list.html.twig', [
            "reservations"=>$reservations,
            'event'=>$event,
            'statsEvent'=>$statsEvent,
        ]);

        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Ajouter un pied de page à chaque page
        $canvas = $dompdf->getCanvas();
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            $font = $fontMetrics->get_font('helvetica');
            $pageText = 'Page ' . $pageNumber . ' sur ' . $pageCount;
            $canvas->text(520, 15, $pageText, $font, 12);
        });

        // Output the generated PDF to Browser
        $dompdf->stream('liste-evenement.pdf', [
            // permet d'ouvrir le fichier dans le pdf
            'Attachment'=>false,
        ]);

        exit();
    }
}
