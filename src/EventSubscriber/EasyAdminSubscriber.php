<?php

namespace App\EventSubscriber;

use App\Entity\StatsVideo;
use App\Entity\Video;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\StatsVideoRepository;
use App\Repository\VideoRepository;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $statsVideoRepository;
    private $entityManagerInterface;

    public function __construct(StatsVideoRepository $statsVideoRepository, EntityManagerInterface $entityManagerInterface){
        $this->statsVideoRepository=$statsVideoRepository;
        $this->entityManagerInterface=$entityManagerInterface;

    }

    public function onCreation(AfterEntityPersistedEvent $event){
        $entity = $event->getEntityInstance();

        // créer une ligne stats video à chaque création de nouvelle vidéo
        $statsVideo = new StatsVideo();
        $statsVideo->setVideoId($entity->getId());
        $statsVideo->setVideoName($entity->getName());
        if($statsVideo->getPlayCount() == NULL){
            $statsVideo->setPlayCount(0);
            // die('null');
        }else{
            $statsVideo->setPlayCount($statsVideo->getPlayCount()+1);
        }
        // dd($statsVideo);
        $this->entityManagerInterface->persist($statsVideo);
        $this->entityManagerInterface->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
            AfterEntityPersistedEvent::class => ['onCreation'],
        ];
    }
}