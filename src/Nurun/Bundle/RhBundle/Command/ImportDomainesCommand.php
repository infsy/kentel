<?php

namespace Nurun\Bundle\RhBundle\Command;
 
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
 
use Nurun\Bundle\RhBundle\Entity\Domaine;

 
class ImportDomainesCommand extends ContainerAwareCommand
{
 
    protected function configure()
    {
        // Name and description for app/console command
        $this
        ->setName('importDomaines:csv')
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
        
        // Processing on each row of data
        foreach($data as $row) {
 
            $domaine = $em->getRepository('NurunRhBundle:Domaine')
                       ->findOneByDescription($row['domaine']); 
            // If the conseiller doest not exist we alert
            if(!is_object($domaine)){
                $output->writeln('<error>Le domaine '. $row['domaine']. ' doit être créé</error>');   
                $domaine = new Domaine();
                $domaine->setDescription($row['domaine']);
                $em->persist($domaine);
            }
            
            if (($i % $batchSize) === 0) 
            {
                $em->flush();
                $em->clear();

                // Advancing for progress display on console
                $progress->advance($batchSize);
                $now = new \DateTime();
                $output->writeln(' of domaine imported ... | ' . $now->format('d-m-Y G:i:s'));
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