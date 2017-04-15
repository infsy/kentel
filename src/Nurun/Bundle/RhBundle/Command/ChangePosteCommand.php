<?php

namespace Nurun\Bundle\RhBundle\Command;
 
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
 
use Nurun\Bundle\RhBundle\Entity\RoleConseiller;
use Nurun\Bundle\RhBundle\Entity\PosteConseiller;
use Nurun\Bundle\RhBundle\Entity\Conseiller;
 
class ChangePosteCommand extends ContainerAwareCommand
{
 
    protected function configure()
    {
        // Name and description for app/console command
        $this
        ->setName('change:postes')
        ->setDescription('Fixe des valeurs par défaut')
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
        $this->change($input, $output);
        
        // Showing when the script is over
        $now = new \DateTime();
        $output->writeln('<comment>End : ' . $now->format('d-m-Y G:i:s') . ' ---</comment>');
    }
    
    protected function change(InputInterface $input, OutputInterface $output)
    {
        // Getting php array of data from CSV
        $data = $this->get($input, $output);
        
        // Getting doctrine manager
        $em = $this->getContainer()->get('doctrine')->getManager();
        
        // Turning off doctrine default logs queries for saving memory
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        
        $conseillers = $em->getRepository('NurunRhBundle:Conseiller')
                       ->findAll();    
        
                
        // Define the size of record, the frequency for persisting the data and the current index of records
        $size = count($conseillers);
        $batchSize = 10;
        $i = 1;
        
        // Starting progress
        $progress = new ProgressBar($output, $size);
        $progress->start();

        // Processing on each row of data
        foreach($data as $row) 
        {
 
            $conseiller = $em->getRepository('NurunRhBundle:Conseiller')
                       ->findOneByName($row['nom'], $row['prenom']);
            
            // If the user doest not exist we create one
            if(!is_object($conseiller)){
            $output->writeln('<error>Le conseiller '. $row['prenom']. ' '. $row['nom'].' ne se trouve pas</error>');                
            }
            else
            {
                $role = $em->getRepository('NurunRhBundle:RoleConseiller')
                       ->findOneByDescription($row['role']);    
                if(!is_object($role))
                {
                $role = $em->getRepository('NurunRhBundle:RoleConseiller')
                       ->findOneByDescription('Conseiller');
                }
                $poste = $em->getRepository('NurunRhBundle:PosteConseiller')
                       ->findOneByDescription($row['poste']);    
                if(!is_object($poste))
                {
                $poste = $em->getRepository('NurunRhBundle:PosteConseiller')
                       ->findOneByDescription('Conseillèr(e) en analyse technologique 1');
                }
                $conseiller->setRole($role);
                $conseiller->setPoste($poste);
                $em->merge($conseiller);
                
                if (($i % $batchSize) === 0) 
                {
                    $em->flush();
                    $em->clear();

                    // Advancing for progress display on console
                    $progress->advance($batchSize);
                    $now = new \DateTime();
                    $output->writeln(' of users modified ... | ' . $now->format('d-m-Y G:i:s'));
                }
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