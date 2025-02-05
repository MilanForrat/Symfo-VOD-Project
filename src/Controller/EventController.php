<?php

namespace App\Controller;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class EventController extends AbstractController
{
    #[Route('/evenement', name: 'app_all_events')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $evenement = $entityManager->getRepository(Event::class)->findAll();

        return $this->render('event/index.html.twig', [
            'events' => $evenement,
        ]);
    }

    #[Route('/evenement/{id}', name: 'app_event_details')]
    public function evenementById(EntityManagerInterface $entityManager, int $id): Response
    {
        $evenement = $entityManager->getRepository(Event::class)->findOneById($id);

        if(!$evenement){
            return $this->redirectToRoute('app_home');
        }

        return $this->render('event/details.html.twig', [
            'event' => $evenement,
        ]);
    }

    #[Route('/evenement/reservation/{id}', name: 'app_event_reservation')]
    public function evenementReservation(EntityManagerInterface $entityManager, int $id): Response
    {
        $evenement = $entityManager->getRepository(Event::class)->findOneById($id);

        if(!$evenement){
            return $this->redirectToRoute('app_home');
        }

        return $this->render('event/reservation_events.html.twig', [
            'event' => $evenement,
        ]);
    }
}
