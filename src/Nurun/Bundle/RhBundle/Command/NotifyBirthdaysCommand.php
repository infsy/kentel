<?php

namespace Nurun\Bundle\RhBundle\Command;
 
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class NotifyBirthdaysCommand extends ContainerAwareCommand
{
 
    protected function configure()
    {
        // Name and description for app/console command
        $this
        ->setName('kentel:notifybirthdays')
        ->setDescription('Vérifie les anniversaires du jour et écrit un courriel à leur entourage')
                ;
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Importing CSV on DB via Doctrine ORM and update DB
        $this->update($input, $output);
    }
    
    protected function update(InputInterface $input, OutputInterface $output)
    {
        // Getting the actual day
        $today = new \DateTime();

        // Getting doctrine manager
        $em = $this->getContainer()->get('doctrine')->getManager();

        // Get all conseillers
        $conseillers = $em->getRepository('NurunRhBundle:Conseiller')->findAll();

        // Turning off doctrine default logs queries for saving memory
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        
        // Processing on each item of conseillers
        foreach($conseillers as $conseiller) {
             if (!empty($conseiller->getDateFete()))
            {
                if ($conseiller->getDateFete()->format('d-m') == $today->format('d-m'))
                {
                    // We prepare the HTML code which will be send
                    $alert = $this->getContainer()->get('templating')->render('NurunRhBundle:Conseiller:birthdayEmail.txt.twig', array('conseiller' => $conseiller));
                    $notify = $this->getContainer()->get('send.email');

                    // We prepare the list of additionnal destinataires (like RGE)
                    $additionnalDestinataires = array();
                    $rdp = $em->getRepository('NurunRhBundle:ConseillerRdp')->findActifRdp($conseiller);

                    // if $conseiller have an active RGE, we add him to the list
                    if (!empty($rdp))
                    {
                        $additionnalDestinataires[] = $rdp->getRdp()->getCourriel();
                    }

                    // We send the email
                    $notify->notifyVp('Fête ton collègue', $alert, $conseiller->getVicePresidence()->getAcronyme(), $additionnalDestinataires, null);

                    $body = $this->getContainer()->get('templating')->render('NurunRhBundle:Conseiller:birthdayEmail.txt.twig', array('conseiller' => $conseiller));
                    $notification = $this->getContainer()->get('nurun.notification');
                    $notification->notify('kentel:notifybirthdays', $body, $conseiller);
                }
            }
        }
    }
}