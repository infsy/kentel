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
use Nurun\Bundle\RhBundle\Entity\Mandat;
use Nurun\Bundle\RhBundle\Entity\Client;
use Nurun\Bundle\RhBundle\Entity\VicePresidence;
use Symfony\Component\Validator\Constraints\False;


class UpdateMandatsCommand extends ContainerAwareCommand
{
 
    protected function configure()
    {
        // Name and description for app/console command
        $this
        ->setName('updateMandats:csv')
        ->setDescription('Update Mandats from CSV file.')
        ->addArgument(
            'file',
            InputArgument::REQUIRED,
            'chemin complet du fichier à charger')
                ;
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Showing when the script is launched
        $now = new \DateTime();
        $output->writeln('<comment>Start : ' . $now->format('d-m-Y G:i:s') . ' ---</comment>');
        
        // Importing CSV on DB via Doctrine ORM
        $this->update($input, $output);
        
        // Showing when the script is over
        $now = new \DateTime();
        $output->writeln('<comment>End : ' . $now->format('d-m-Y G:i:s') . ' ---</comment>');
    }
    
    protected function update(InputInterface $input, OutputInterface $output)
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
        foreach($data as $row) {

            $mandat = $em->getRepository('NurunRhBundle:Mandat')
                ->findOneByIdentifiant($row['Numéro']);

            if(!is_object($mandat)){
                $output->writeln('<error>Le mandat '. $row['Client']. '-'.$row['Numéro']. ' ne se trouve pas dans la base</error>');
            }

            else {
                if (!empty($row['Email CP'])) {
                    $cp = $em->getRepository('NurunRhBundle:Conseiller')
                        ->findOneByCourriel($row['Email CP']);
                    if (!is_object($cp)) {
                        $output->writeln('<error>Le cp ' . $row['Email CP'] . ' ne se trouve pas dans la base</error>');
                    } else {
                        if (!is_object($mandat->getChargeprojet())) {
                            $output->writeln('<info>Mise à jour du CP du ' . $row['Client'] . '-' . $row['Numéro'] . '</info>');
                            $mandat->setChargeprojet($cp);
                        } else {
                            if ($cp->getNom() != $mandat->getChargeprojet()->getNom()) {
                                $output->writeln('<info>Mise à jour du CP du ' . $row['Client'] . '-' . $row['Numéro'] . '</info>');
                                $mandat->setChargeprojet($cp);
                            }
                        }
                    }
                }

                if (!empty($row['Email Mandataire'])) {
                    $mandataire = $em->getRepository('NurunRhBundle:Conseiller')
                        ->findOneByCourriel($row['Email Mandataire']);
                    if (!is_object($mandataire)) {
                        $output->writeln('<error>Le mandataire ' . $row['Email Mandataire'] . ' ne se trouve pas dans la base</error>');
                    } else {
                        if (!is_object($mandat->getMandataire())) {
                            $output->writeln('<info>Mise à jour du mandataire du ' . $row['Client facturé'] . '-' . $row['Numéro'] . '</info>');
                            $mandat->setMandataire($mandataire);
                        } else {
                            if ($mandataire->getNom() != $mandat->getMandataire()->getNom()) {
                                $output->writeln('<info>Mise à jour du mandataire du ' . $row['Client facturé'] . '-' . $row['Numéro'] . '</info>');
                                $mandat->setMandataire($mandataire);
                            }
                        }
                    }
                }

                if (!empty($row['No'])) {
                    $mandat->setNumeroAdresse($row['No']);
                }

                if (!empty($row['Rue'])) {
                    $mandat->setLigne1Adresse($row['Rue']);
                }

                if (!empty($row['Bureau'])) {
                    $mandat->setLigne2Adresse($row['Bureau']);
                }

                if (!empty($row['Étage'])) {
                    $mandat->setLigne3Adresse($row['Étage']);
                }

                if (!empty($row['Ville'])) {
                    $mandat->setVille($row['Ville']);
                }

                if (!empty($row['Code Postal'])) {
                    $mandat->setCodePostal($row['Code Postal']);
                }

                $em->persist($mandat);

                unset($mandat);
                unset($cp);
                unset($mandataire);

                if (($i % $batchSize) === 0) {
                    $em->flush();
                    $em->clear();

                    // Advancing for progress display on console
                    $progress->advance($batchSize);
                    $now = new \DateTime();
                    $output->writeln(' of mandats updated ... | ' . $now->format('d-m-Y G:i:s'));
                }

                $i++;
            }
        }
        
        // Flushing and clear data on queue
        $em->flush();
        $em->clear();
        // Ending the progress bar process
        $progress->finish();
    }
 
    protected function get(InputInterface $input, OutputInterface $output)
    {
        if ($filename = $input->getArgument('file'))
        {
            // Using service for converting CSV to PHP Array
            $converter = $this->getContainer()->get('import.csvtoarray');
            $data = $converter->convert($filename, ',');
        
            return $data;
        }
        else
        {
            exit(1);   
        }
    }
    
}