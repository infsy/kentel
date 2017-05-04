<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function init() {
        // Change timezone
        date_default_timezone_set('America/Montreal');

        parent::init();
    }
    
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Nurun\Bundle\RhBundle\NurunRhBundle(),
	    new Ddeboer\DataImportBundle\DdeboerDataImportBundle(),
            new APY\DataGridBundle\APYDataGridBundle(),
            new Sg\DatatablesBundle\SgDatatablesBundle(),
            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),
            new Craue\FormFlowBundle\CraueFormFlowBundle(),
            new Nurun\Bundle\UserBundle\NurunUserBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Kitpages\DataGridBundle\KitpagesDataGridBundle(),
            new Genemu\Bundle\FormBundle\GenemuFormBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new Knp\DoctrineBehaviors\Bundle\DoctrineBehaviorsBundle(),
            new Nurun\Bundle\SystemBundle\NurunSystemBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
