<?php

namespace Nurun\Bundle\SystemBundle\Services;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Monolog\Logger;
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 15-12-19
 * Time: 16:27
 */

/**
 * AlertAdmin permet de notifier l'administrateur définit dans l'application en cas d'alertes
 *
 * @author cedric
 */
class SendEmail
{
    private $entityManager;
    private $mailer;
    private $logger;

    public function __construct(EntityManager $entityManager, \Swift_Mailer $mailer, Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->mailer    = $mailer;
        $this->logger    = $logger;

    }

    public function notifyAdmin($alert)
    {

        $system = $this->entityManager->getRepository('NurunSystemBundle:System')->findAll();
        $destinataire = $system[0]->getEmailAdministrateur();
        $message = \Swift_Message::newInstance()
            ->setFrom(array('nurun.kentel@gmail.com' => 'Application KENTEL'))
            ->setReturnPath($destinataire)
            ->setSubject('[ KENTEL ] Notification système')
            ->setTo($destinataire)
            ->setBody($alert, 'text/plain');

        $this->mailer->send($message);
    }

    public function notifyVp($object, $alert, $vp, $destinataire, $user)
    {


        #$this->logger->warning('Envoi message');

        // On récupère la liste des correspondants relatifs aux affectations de la VP du conseiller

        $system = $this->entityManager->getRepository('NurunSystemBundle:System')->findAll();
        switch ($vp) {
            case "VPTS";
                $destinataires = $system[0]->getEmailVpts();
                break;
            case "VPAS";
                $destinataires = $system[0]->getEmailVpas();
                break;
            case "VPSI";
                $destinataires = $system[0]->getEmailVpsi();
                break;
            default:
                $destinataires = $system[0]->getEmailVpts();
        }
        $destinatairesToArray = explode(",", $destinataires);

        // on Crée le conteneur message de type Swift_Message

        $message = \Swift_Message::newInstance()
            ->setFrom(array('nurun.kentel@gmail.com' => 'Application KENTEL'))
            ->setSubject('[ KENTEL ] '.$object)
            ->setTo($destinatairesToArray)
            ->setBody($alert, 'text/html');

        // On va ajouter les destinataires complémentaires relatifs au contexte
        if (!empty($destinataire)) {
            foreach ($destinataire as $courriel) {
                $message->addTo(trim($courriel));
               # $this->logger->warning('et on a ajouté : ' . $courriel);
            }
        }

//        $this->logger->warning('Le total '.var_dump($destinatairesToArray));

        // On positionne l'adresse de retour
        if (!empty($user))
        {
            $message->setReturnPath($user->getEmail());
        }
        else
        {
            $message->setReturnPath('kentel@nurunquebec.com');
        }

        // et on envoie le tout
        $this->mailer->send($message);
    }
}