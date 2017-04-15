<?php

namespace Nurun\Bundle\RhBundle\Command;
 
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

use Ivory\GoogleMap\Service\Geocoder\GeocoderService;
use Http\Adapter\Guzzle6\Client;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Ivory\GoogleMap\Service\Geocoder\Request\GeocoderAddressRequest;


use Nurun\Bundle\RhBundle\Entity\Adresse;
use Nurun\Bundle\RhBundle\Entity\Mandat;

use Symfony\Component\Validator\Constraints\False;


class ImportAdressesCommand extends ContainerAwareCommand
{
 
    protected function configure()
    {
        // Name and description for app/console command
        $this
        ->setName('nurun:importAdresses')
        ->setDescription('import Adresses from CSV file.')
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

        // construction du geocoder
        $geocoder = new GeocoderService(new Client(), new GuzzleMessageFactory());

        // Processing on each row of data
        foreach($data as $row) {

            $mandat = $em->getRepository('NurunRhBundle:Mandat')
                ->findOneByIdentifiant($row['Numéro']);

            if(!is_object($mandat)){
                $output->writeln('<error>Le mandat '. $row['Client']. '-'.$row['Numéro']. ' ne se trouve pas dans la base</error>');
            }

            else {
//                Ce bout de code sert à vider les adresses déjà rattachées au mandat. Ne fonctionne pas
//                si on a plusieurs adresses attachées à 1 même mandat dans le fichier de chargement
//                $adresses = $mandat->getAdresses();
//                foreach ($adresses as $adresse) {
//                    $mandat->removeAdress($adresse);
//                }
//                $em->flush();
                if (!empty($row['Code Postal'])) {
                    $adresse = $em->getRepository('NurunRhBundle:Adresse')
                        ->findOneByCodeAndNumber($row['Code Postal'], $row['No']);
                    if (empty($adresse)) {
                        $adresse = new Adresse();
                        $adresse->setCodePostal($row['Code Postal']);
                        $adresse->setLigne1Adresse($row['Rue']);
                        $adresse->setLigne2Adresse($row['Bureau']);
                        $adresse->setLigne3Adresse($row['Étage']);
                        $adresse->setNumeroAdresse($row['No']);
                        $adresse->setVille($row['Ville']);
                        $em->persist($adresse);
                        $em->flush();
                    }
                    if (empty($adresse->getLatitude())) {
                        $adresseString = $row['No']. ' ' . $row['Rue']. ', ' . $row['Ville'] . ', CA';
                        $request = new GeocoderAddressRequest($adresseString);
                        $result = $geocoder->geocode($request);
                        if ($result->getStatus() != "OK") {
                            $output->writeln('<error>L"adresse '. $row['Code Postal']. ' ne se retrouve pas dans Google</error>');
                        }
                        else {
                            $geometryArray=$result->getResults();
                            $geometryObject=$geometryArray[0];
                            $adresse->setLatitude($geometryObject->getGeometry()->getLocation()->getLatitude());
                            $adresse->setLongitude($geometryObject->getGeometry()->getLocation()->getLongitude());
                            $em->flush();
                        }
                }
                    $mandat->addAdress($adresse);
            }

                $em->persist($mandat);
                $em->flush();

//                unset($mandat);

                if (($i % $batchSize) === 0) {
                    $em->flush();
                    $em->clear();

                    // Advancing for progress display on console
                    $progress->advance($batchSize);
                    $now = new \DateTime();
                    $output->writeln(' of adresse updated ... | ' . $now->format('d-m-Y G:i:s'));
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