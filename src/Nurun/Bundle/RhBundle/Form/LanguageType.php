<?php

namespace Nurun\Bundle\RhBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LanguageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('language',   'text', array(
                                            'required' => true))
            ->add('code',       'text', array(
                                            'required' => true))
            ->add('save',       'submit',array(
                                            'label'=>'enregistrer'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nurun\Bundle\RhBundle\Entity\Language'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nurun_bundle_rhbundle_language';
    }
}
