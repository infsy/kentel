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
use Nurun\Bundle\RhBundle\Entity\Conseiller;
 
class ChangeDomaineCommand extends ContainerAwareCommand
{
 
    protected function configure()
    {
        // Name and description for app/console command
        $this
        ->setName('change:domaine')
        ->setDescription('Fixe des valeurs par défaut')
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
        
        // Getting doctrine manager
        $em = $this->getContainer()->get('doctrine')->getManager();
        
        // Turning off doctrine default logs queries for saving memory
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        
        $conseillers = $em->getRepository('NurunRhBundle:Conseiller')
                       ->findAll();    
        
        $defaultDomaine = $em->getRepository('NurunRhBundle:Domaine')
                       ->findOneByDescription('Techno/Sécurité');
        
        
        // Define the size of record, the frequency for persisting the data and the current index of records
        $size = count($conseillers);
        $batchSize = 10;
        $i = 1;
        
        // Starting progress
        $progress = new ProgressBar($output, $size);
        $progress->start();

        // Processing on each row of data
        foreach($conseillers as $conseiller) {
 
            $conseiller->setDomaine($defaultDomaine);
            
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
 
                    $i++;
            
        }
        
// Flushing and clear data on queue
        $em->flush();
        $em->clear();
// Ending the progress bar process
        $progress->finish();
    }
    
}