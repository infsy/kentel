<?php

namespace Nurun\Bundle\RhBundle\Command;

use Nurun\Bundle\RhBundle\Entity\ConseillerCompetence;
use Nurun\Bundle\RhBundle\Entity\ConseillerLanguage;
use Nurun\Bundle\RhBundle\Entity\Niveau;
use Nurun\Bundle\RhBundle\Entity\TypeCompetence;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

use Nurun\Bundle\RhBundle\Entity\Conseiller;
use Nurun\Bundle\RhBundle\Entity\Competence;


class ImportExpertisesCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('nurun:import:expertises')
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
            // on récupère le conseiller
            $conseiller = $em->getRepository('NurunRhBundle:Conseiller')
                ->findOneByCourriel($row['Courriel']);

            if (empty($conseiller)) {
                $now = new \DateTime();
                $output->writeln('<error> Conseiller ' . $row['Courriel'] . ' est introuvable
                  | ' . $now->format('d-m-Y G:i:s'));

            } // on met à jour ses infos
            else {
                //on récupère l'expertise  ou la créons si n'existe pas encore en base
                if (!empty($row['Expertise'])) {
                    $expertise = $em->getRepository('NurunRhBundle:Competence')
                        ->findOneByCompetence($row['Expertise']);
                    if (empty($expertise)) {
                        $expertise = new Competence();
                        $expertise->setCompetence($row['Expertise']);
                        if (!empty($row['Type'])) {
                            $typeExpertise = $em->getRepository('NurunRhBundle:TypeCompetence')
                                ->findOneByType($row['Type']);
                            if (empty($typeExpertise)) {
                                $typeExpertise = new TypeCompetence();
                                $typeExpertise->setType($row['Type']);
                                $em->persist($typeExpertise);
                                $em->flush();
                            }
                        }
                        $expertise->setType($typeExpertise);
                        $em->persist($expertise);

                    }
                    $conseillerCompetence = new ConseillerCompetence();
                    $conseillerCompetence->setCompetence($expertise);
                    if (!empty($row['Niveau expertise'])) {
                        $niveauCompetence = $em->getRepository('NurunRhBundle:Niveau')
                            ->findOneByNiveau($row['Niveau expertise']);
                        if (empty($niveauCompetence)) {
                            $niveauCompetence = new Niveau();
                            $niveauCompetence->setNiveau($row['Niveau expertise']);
                            $niveauCompetence->setForce(intval($row['code1']));
                            $em->persist($niveauCompetence);
                        }
                    } elseif (!empty($row['Années expérience'])) {
                        $niveauCompetence = $em->getRepository('NurunRhBundle:Niveau')
                            ->findOneByNiveau($row['Années expérience']);
                        if (empty($niveauCompetence)) {
                            $niveauCompetence = new Niveau();
                            $niveauCompetence->setNiveau($row['Années expérience']);
                            $niveauCompetence->setForce(intval($row['code2']));
                            $em->persist($niveauCompetence);
                        }
                    }
                    $conseillerCompetence->setNiveau($niveauCompetence);
                    $conseillerCompetence->setConseiller($conseiller);
                    $em->persist($conseillerCompetence);

//                    $conseiller->addCompetence($conseillerCompetence);

                } elseif (!empty($row['Niveau anglais'])) {
                    $niveau = $em->getRepository('NurunRhBundle:Niveau')
                        ->findOneByNiveau($row['Niveau anglais']);
                    if (empty($niveau)) {
                        $niveau = new Niveau();
                        $niveau->setNiveau($row['Niveau anglais']);
                        $niveau->setForce(intval($row['code3']));
                        $em->persist($niveau);
                    }
                    $conseillerLanguage = new ConseillerLanguage();
                    $conseillerLanguage->setNiveau($niveau);
                    $language = $em->getRepository('NurunRhBundle:Language')
                        ->findOneByCode('EN');
                    $conseillerLanguage->setLanguage($language);
                    $conseillerLanguage->setConseiller($conseiller);
                    $em->persist($conseillerLanguage);
//                    $conseiller->addLanguage($conseillerLanguage);

                } elseif (!empty($row['Contextes'])) {
                    $conseiller->setContextes($row['Contextes']);
                } elseif (!empty($row['Commentaires'])) {
                    $conseiller->setConsigne($row['Commentaires']);
                }
                $em->persist($conseiller);
                $em->flush();
            }

            if (($i % $batchSize) === 0) {
//                $em->flush();
//                $em->clear();

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

    protected
    function get(InputInterface $input, OutputInterface $output)
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