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
use Nurun\Bundle\RhBundle\Entity\PosteConseiller;
use Nurun\Bundle\RhBundle\Entity\RoleConseiller;
use Nurun\Bundle\RhBundle\Entity\Domaine;
use Nurun\Bundle\RhBundle\Entity\StatutConseiller;
use Nurun\Bundle\RhBundle\Entity\VicePresidence;


 
class UpdateConseillersCommand extends ContainerAwareCommand
{
 
    protected function configure()
    {
        // Name and description for app/console command
        $this
        ->setName('updateConseillers:csv')
        ->setDescription('Mise à jour des conseillers selon des données CSV')
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
        
        // Importing CSV on DB via Doctrine ORM and update DB
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
 
            // $domaine = $em->getRepository('NurunRhBundle:Domaine')
            //            ->findOneByDescription('Solutions informatique'); 
            // $poste = $em->getRepository('NurunRhBundle:PosteConseiller')
            //            ->findOneByDescription($row['Poste']); 
            // $role = $em->getRepository('NurunRhBundle:RoleConseiller')
            //            ->findOneByDescription($row['Rôle']);
            // $statut = $em->getRepository('NurunRhBundle:StatutConseiller')
            //            ->findOneByDescription($row['Statut']); 
            // $vp = $em->getRepository('NurunRhBundle:VicePresidence')
            //            ->findOneByAcronyme('VPSI');
            
            $conseillerpresent = $em->getRepository('NurunRhBundle:Conseiller')
                       ->findOneByName($row['Nom'], $row['Prénom']);
            
            if (empty($conseillerpresent))
            {
                $output->writeln('<error> Conseiller '.$row['Nom'].' '.$row['Prénom']. ' non trouvé en base ---</error>');
            }
            else
            {
                $conseiller = $conseillerpresent;          
                $conseiller->setNumEmploye($row['EmpNo']); 
                $conseiller->setDateArrivee(new \DateTime($row['Date d\'arrivée']));
                $conseiller->setDateFete(new \DateTime($row['Date de naissance']));
                $conseiller->setNbreHeures($row['Horaire de travail']);
                $conseiller->setPrenom($row['Prénom']);
                $conseiller->setNom($row['Nom']);
                $conseiller->setCourriel($row['courriel']);
                // $conseiller->setPoste($poste);
                // $conseiller->setRole($role);
                // $conseiller->setDomaine($domaine);
                // $conseiller->setStatut($statut);
                // $conseiller->setVicePresidence($vp);

           
            $em->persist($conseiller);
            
            }

            if (($i % $batchSize) === 0) 
            {
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