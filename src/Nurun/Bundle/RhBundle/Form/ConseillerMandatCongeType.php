<?php

namespace Nurun\Bundle\RhBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConseillerMandatCongeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
             ->add('conseiller', 'entity', array(
                'class' => 'NurunRhBundle:Conseiller',
                 'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\ConseillerRepository $er) {
                return $er->createQueryBuilder('u')
                    ->orderBy('u.prenom', 'ASC');
                 },
                 'required' => true))
//                'property' => 'display',
//                'multiple' => false,
//                'required' => true))           
            ->add('dateDebut', 'genemu_jquerydate', array('widget' => 'single_text'))
            ->add('dateFin', 'genemu_jquerydate', array('widget' => 'single_text'))
            ->add('statutAffectation', 'entity', array(
                'class' => 'NurunRhBundle:StatutAffectation',
                 'property' => 'acronyme',
                 'multiple' => false,
                 'required' => false))
            ->add('pourcentage', 'text')
             ->add('save',      'submit')
            ;
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
        return 'nurun_bundle_rhbundle_conseillerMandat';
    }
}
