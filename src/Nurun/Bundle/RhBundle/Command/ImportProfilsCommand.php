<?php

namespace Nurun\Bundle\RhBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

use Nurun\Bundle\RhBundle\Entity\Conseiller;
use Nurun\Bundle\RhBundle\Entity\ProfilConseiller;

class ImportProfilsCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('nurun:import:profils')
            ->setDescription('Import data from CSV file')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'chemin complet du fichier à charger');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Showing when the script is launched
        $now = new \DateTime();
        $output->writeln('<comment>Start : ' . $now->format('d-m-Y G:i:s') . ' ---</comment>');

        // Importing CSV on DB via Doctrine ORM
        $this->import($input, $output);

        // Showing when the script is over
        $now = new \DateTime();
        $output->writeln('<comment>End : ' . $now->format('d-m-Y G:i:s') . ' ---</comment>');
    }

    protected function import(InputInterface $input, OutputInterface $output)
    {
        // Getting php array of data from CSV
        $data = $this->get($input, $output);

        // Getting doctrine manager
        $em = $this->getContainer()->get('doctrine')->getManager();

        // Turning off doctrine default logs queries for saving memory
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        // Define the size of record, the frequency for persisting the data and the current index of records
        $size = count($data);
        $batchSize = 10;
        $i = 1;

        // Starting progress
        $progress = new ProgressBar($output, $size);
        $progress->start();

        // Processing on each row of data
        foreach ($data as $row) {

            //on récupère le profil principal ou le créons si n'existe pas encore en base
            if (!empty($row['Profil principal'])) {
                $profilPrincipal = $em->getRepository('NurunRhBundle:ProfilConseiller')
                    ->findOneByProfil($row['Profil principal']);
                if (empty($profilPrincipal)) {
                    $profilPrincipal = new ProfilConseiller();
                    $profilPrincipal->setProfil($row['Profil principal']);
                    $em->persist($profilPrincipal);
                    $em->flush();
                }
            }

            if (!empty($row['Profil secondaire'])) {
                // on récupère le profil secondaire ou le crééons si n'existe pas encore en base
                $profilSecondaire = $em->getRepository('NurunRhBundle:ProfilConseiller')
                    ->findOneByProfil($row['Profil secondaire']);
                if (empty($profilSecondaire)) {
                    $profilSecondaire = new ProfilConseiller();
                    $profilSecondaire->setProfil($row['Profil secondaire']);
                    $em->persist($profilSecondaire);
                    $em->flush();
                }
            }

            // on récupère le conseiller
            $conseiller = $em->getRepository('NurunRhBundle:Conseiller')
                ->findOneByCourriel($row['courriel']);

            if (empty($conseiller)) {
                $now = new \DateTime();
                $output->writeln('<error> Conseiller ' . $row['courriel'] . ' est introuvable
                  | ' . $now->format('d-m-Y G:i:s'));

            } // on met à jour ses infos
            else {
                $conseiller->setNumEmploye($row['EmpNo']);
                if (!empty($profilPrincipal)) {
                    $conseiller->setProfil($profilPrincipal);
                }
                if (!empty($profilSecondaire)) {
                    $conseiller->setProfilSecondaire($profilSecondaire);
                }
                $conseiller->setDateArrivee(new \DateTime($row['Date arrivée']));
                $conseiller->setExperienceAnnees($row['annees']);
                $conseiller->setExperienceMois($row['mois']);

                $em->persist($conseiller);
            }



            if (($i % $batchSize) === 0) {
                $em->flush();
                $em->clear();

                // Advancing for progress display on console
                $progress->advance($batchSize);
                $now = new \DateTime();
                $output->writeln(' of conseillers imported ... | ' . $now->format('d-m-Y G:i:s'));
            }

            $i++;
        }

        // Flushing and clear data on queue
        $em->flush();
        $em->clear();
        // Ending the progress bar process
        $progress->finish();
    }

    protected function get(InputInterface $input, OutputInterface $output)
    {
        if ($filename = $input->getArgument('file')) {
            // Using service for converting CSV to PHP Array
            $converter = $this->getContainer()->get('import.csvtoarray');
            $data = $converter->convert($filename, ',');

            return $data;
        } else {
            exit(1);
        }
    }

}