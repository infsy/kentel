<?php

// AdresseType

namespace Nurun\Bundle\RhBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdresseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numeroAdresse',     'integer', array(
                'required' => false
            ))
            ->add('ligne1Adresse',    'text', array(
                'required' => false
            ))
            ->add('ligne2Adresse',    'text', array(
                'required' => false
            ))
            ->add('ligne3Adresse',    'text', array(
                'required' => false
            ))
            ->add('codepostal',    'text', array(
                'required' => false
            ))
            ->add('ville',    'text', array(
                'required' => false
            ))
            ->add('save',           'submit',array('label'=>'Enregistrer'))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nurun\Bundle\RhBundle\Entity\Adresse'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nurun_bundle_rhbundle_adresse';
    }
}