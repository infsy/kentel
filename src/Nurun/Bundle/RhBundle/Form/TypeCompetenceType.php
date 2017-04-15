<?php

namespace Nurun\Bundle\RhBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeCompetenceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type',   'text')
            ->add('save',   'submit',array('label'=>'enregistrer'))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nurun\Bundle\RhBundle\Entity\TypeCompetence'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nurun_bundle_rhbundle_competence_type';
    }
}