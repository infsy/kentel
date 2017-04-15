<?php

namespace Nurun\Bundle\SystemBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;



class TestEmailCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        // Name and description for app/console command
        $this
            ->setName('TestEmail')
            ->setDescription('Test le service de courriel')
            ->addArgument(
                'message',
                InputArgument::REQUIRED,
                'message a envoyer par courriel')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Showing when the script is launched
        $now = new \DateTime();
        $output->writeln('<comment>Start : ' . $now->format('d-m-Y G:i:s') . ' ---</comment>');

        // Importing CSV on DB via Doctrine ORM
        $this->testCourriel($input, $output);

        // Showing when the script is over
        $now = new \DateTime();
        $output->writeln('<comment>End : ' . $now->format('d-m-Y G:i:s') . ' ---</comment>');
    }

    protected function testCourriel(InputInterface $input, OutputInterface $output)
    {
//        Starting progress Bar
        $progress = new ProgressBar($output, 100);
        $progress->start();
        $progress->advance(50);

        // Getting php array of data from CSV
        $notify = $this->getContainer()->get('send.email');
        $notify->notifyAdmin($input->getArgument('message'));
        $progress->advance(40);

        // Finishing progress

        $progress->finish();

    }


}