<?php

namespace Nurun\Bundle\SystemBundle\Services;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Notification permet d'envoyer des messages selon un template pré établi
 *
 * @author jonathan
 */
class Notification
{
    private $entityManager;
    private $mailer;

    public function __construct(EntityManager $entityManager, \Swift_Mailer $mailer, TokenStorage $tokenStorage)
    {
        $this->entityManager    = $entityManager;
        $this->mailer           = $mailer;
        $this->tokenStorage     = $tokenStorage;
    }

    public function notify($route, $body, $object)
    {
        $action = $this->entityManager->getRepository('NurunRhBundle:Action')->findOneByName($route);
        if(!$action){
            //l'action n'a pas encore été ajouté
            return;
        }

        $userNotificationList = $action->getUserNotifications();
        if(!$userNotificationList){
            //personne ne doit être notifié
            return;
        }

        if($this->tokenStorage->getToken()) {
            $user = $this->tokenStorage->getToken()->getUser();
            $emailFrom = $user->getEmail();
        }
        else {
            //on est donc en présence d'un appel d'une commande
            $emailFrom = 'nurun.kentel@gmail.com';
        }

        $message = \Swift_Message::newInstance()
            ->setFrom(array($emailFrom => 'Application KENTEL'))
            ->setSubject('[ KENTEL ] '.$action->getDescription())
            ->setBody($body, 'text/html');
        
        foreach ($userNotificationList as $userNotification) {
            $destinataire = $userNotification->getUser()->getEmail();

            $emailTo = $message->getTo();
            if($emailTo == null || $emailTo == ''){
                $message->setTo($destinataire);
            }
            else {
                $message->addTo($destinataire);
            }            
        }
        $this->mailer->send($message);
    }
}