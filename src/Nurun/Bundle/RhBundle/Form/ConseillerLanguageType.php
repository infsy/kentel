<?php

namespace Nurun\Bundle\RhBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConseillerLanguageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('language', 'entity', array(
                'class' => 'NurunRhBundle:Language',
                'property' => 'language',
                'multiple' => false,
                'required' => true))
            ->add('niveau', 'entity', array(
                'class' => 'NurunRhBundle:Niveau',
                'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\NiveauRepository $er) {
                    return $er->createQueryBuilder('n')
                        ->where('n.domaine = ?1')
                        ->orderBy('n.niveau', 'ASC')
                        ->setParameter(1,'LANGUE');
                },
                 'required' => true))
             ->add('save',      'submit')
            ;
    }
    
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nurun\Bundle\RhBundle\Entity\ConseillerLanguage'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nurun_bundle_rhbundle_conseillerLanguage';
    }
}
