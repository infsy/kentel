<?php

namespace Nurun\Bundle\RhBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConseillerMandatEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut', 'genemu_jquerydate', array('widget' => 'single_text'))
            ->add('dateFin', 'genemu_jquerydate', array('widget' => 'single_text'))
            ->add('statutAffectation', 'entity', array(
                'class' => 'NurunRhBundle:StatutAffectation',
                // 'property' => 'acronyme',
                'multiple' => false,
                'required' => true))
            ->add('pourcentage', 'text')
            ->add('commentaire','textarea',
                array('required' => false))
             ->add('save',      'submit')
        ;

        $conseiller = $builder->getData()->getConseiller();
        if($conseiller){
            $builder->add('conseiller', 'entity', array(
                'class'         => 'NurunRhBundle:conseiller',
                'placeholder'   => '',
                'choices'       => array($conseiller),
                'disabled'      => true
            ));

        }
    }
    
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nurun\Bundle\RhBundle\Entity\ConseillerMandat'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nurun_bundle_rhbundle_conseillerMandat_edit';
    }
}
