<?php

namespace Nurun\Bundle\SystemBundle\Command;
 
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ArchiveOverdueAffectationCommand extends ContainerAwareCommand
{
 
    protected function configure()
    {
        $this
            ->setName('kentel:archiveOverdueAffectation')
            ->setDescription('Archive les affectations et les mandats terminés')
        ;
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $today = new \DateTime("now");

        $listAffectationActives = $em->getRepository('NurunRhBundle:ConseillerMandat')->findActives();
        foreach($listAffectationActives as $affectationActive) {
            $dateFin = $affectationActive->getDateFin();
            if ($dateFin == null) {
                continue;
            }
            $dateInterval = $today->diff($dateFin);
            if (($dateInterval->d >= 7 || $dateInterval->m >= 1) && $dateInterval->invert == 1) {
                $em->remove($affectationActive);
                $em->flush();

                $body = $this->getContainer()->get('templating')->render('NurunRhBundle:ConseillerMandat:email.html.twig', array('affectation' => $affectationActive, 'action' => 'Archivage'));
                $notification = $this->getContainer()->get('nurun.notification');
                $notification->notify('kentel:ArchiveOverdueAffectation', $body, $affectationActive);
            }
        }

//        $listMandatActifs = $em->getRepository('NurunRhBundle:Mandat')->findActives();
//        foreach($listMandatActifs as $mandatActif) {
//            $dateFin = $mandatActif->getDateFin();
//            if ($dateFin == null) {
//                continue;
//            }
//            $dateInterval = $today->diff($dateFin);
//            if ($dateInterval->m >= 1 && $dateInterval->invert == 1) {
//                $em->remove($mandatActif);
//                $em->flush();
//
//                $body = $this->getContainer()->get('templating')->render('NurunRhBundle:Mandat:email.html.twig', array('mandat' => $mandatActif, 'action' => 'Archivage'));
//                $notification = $this->getContainer()->get('nurun.notification');
//                $notification->notify('kentel:ArchiveOverdueAffectation', $body, $mandatActif);
//            }
//        }
    }
}