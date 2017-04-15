<?php

namespace Nurun\Bundle\RhBundle\Command;
 
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
 
use Nurun\Bundle\RhBundle\Entity\ConseillerMandat;
use Nurun\Bundle\RhBundle\Entity\Conseiller;
use Nurun\Bundle\RhBundle\Entity\Mandat;
use Nurun\Bundle\RhBundle\Entity\Client;


 
class ImportAffectationsCommand extends ContainerAwareCommand
{
 
    protected function configure()
    {
        // Name and description for app/console command
        $this
        ->setName('importAffectations:csv')
        ->setDescription('Import data from CSV file')
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
        $boss = $em->getRepository('NurunRhBundle:Conseiller')
                       ->findOneByName('Langlois', 'Guillaume'); 
        $nurun = $em->getRepository('NurunRhBundle:Client')
                       ->findOneByAcronyme('NSC');

        // Processing on each row of data
        foreach($data as $row) {

//            $client = $row['Client'];
            $conseiller = $em->getRepository('NurunRhBundle:Conseiller')
                       ->findOneByName($row['Nom'], $row['Prénom']);            
            if(!is_object($conseiller)){
                $output->writeln('<comment>Conseiller inconnu : ' . $row['Nom'] . ' ' . $row['Prénom'] . ' ---</comment>');
            }
            else
            {
            $mandat = $em->getRepository('NurunRhBundle:Mandat')
                       ->findOneByIdentifiant($row['Contrat']);
            if (empty($mandat))
            {
                $output->writeln('<error>Le mandat '. $row['Contrat']. ' ne se trouve pas, on le créé</error>');
                $mandat = new Mandat();
                $mandat->setChargeprojet($boss);
                $mandat->setMandataire($boss);
                $mandat->setClient($nurun);
                $mandat->setDateFin(new \DateTime());
                $mandat->setIdentifiant($row['Contrat']);
                $mandat->setNbreHeures(37.5);
                $mandat->setSecteur('VPSI');
                $mandat->setTitre('A definir');
                $mandat->setOffre('Solutions informatique');
                $mandat->setType('H');
                $em->persist($mandat);

            }
            
            $statut = $em->getRepository('NurunRhBundle:StatutAffectation')
                       ->findOneByAcronyme($row['Statut']);
            if (empty($statut))
            {
                $output->writeln('<error>Le statut '. $row['Statut']. ' ne se trouve pas</error>');
            }
            
            
          $affectation = new ConseillerMandat();
          $affectation->setConseiller($conseiller);
          $affectation->setDateDebut(new \DateTime($row['Date début']));
          $affectation->setDateFin(new \DateTime($row['Date fin']));
          $affectation->setMandat($mandat);
          $affectation->setPourcentage($row['%']);
          $affectation->setStatutAffectation($statut);
                        
            $em->persist($affectation);
            
            unset($affectation);
            unset($conseiller);
            unset($mandat);
            unset($statut);
            }
            
            if (($i % $batchSize) === 0) 
            {
                $em->flush();
                $em->clear();

                // Advancing for progress display on console
                $progress->advance($batchSize);
                $now = new \DateTime();
                $output->writeln(' of affectations imported ... | ' . $now->format('d-m-Y G:i:s'));
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