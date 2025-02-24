<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Video;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SeasonController extends AbstractController
{
    #[Route('/saison/{season}', name: 'app_season_details')]
    // ajout de "(EntityManagerInterface $entityManager): Response" pour appeler les éléments de la BDD dans l'index
    // ici "int $id" me permet d'accéder à la valeur de l'id
    public function findBySeasonYear(EntityManagerInterface $entityManager, $season): Response
    {
        $seasons=$entityManager->getRepository(Season::class)->findAll();
        $seasonYear = $entityManager->getRepository(Season::class)->findOneBySeasonYear($season);
        $seasonYearId=$seasonYear->getId();
        $video = $entityManager->getRepository(Video::class)->findBySeason($seasonYearId);
        // dd($seasonYear->getId());
 
        if(!$video){
            throw $this->createNotFoundException(
                'Aucune vidéo trouvée pour cette saison : '.$season
            );
        }

        return $this->render('season/details.html.twig', [
            'video' => $video,
            'seasons'=>$seasons,
            'season'=>$season,
        ]);
    }
}
