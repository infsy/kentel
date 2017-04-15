<?php

namespace Nurun\Bundle\SystemBundle\Command;

use Nurun\Bundle\UserBundle\Entity\Permissions;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class UpdatePermissionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('nurun:reset:permissions')
            ->setDescription('to reset all the permissions')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $listUsers = $em->getRepository('NurunUserBundle:User')->findAll();

        foreach ($listUsers as $user) {
            $permis = $user->getPermissions();
            if(!is_null($permis)){
                $user->setPermissions(new Permissions());
                $em->remove($permis);
            } else {
                $user->setPermissions(new Permissions());
            }
        }
        $em->flush();

        $output->writeln('All the permissions have been updated');
    }
}