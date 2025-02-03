<?php

namespace App\EventSubscriber;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    private $security;
    private $entityManagerInterface;

    public function __construct(Security $security, EntityManagerInterface $entityManagerInterface){
        $this->entityManagerInterface=$entityManagerInterface;
        $this->security=$security;
    }
    
    // dernière date de connexion
    public function onLogin(){
        
        // die('je suis un event');

        // on récupère le User via security car nous sommes dans un event
        $user=$this->security->getUser();
        // dd($user);
        $user->setLastLoginAt(new DateTime());
        $this->entityManagerInterface->flush();

    }

    public static function getSubscribedEvents(): array
    {
        // return the subscribed events, their methods and priorities
        return [
            LoginSuccessEvent::class=>'onLogin',
        ];
    }
}