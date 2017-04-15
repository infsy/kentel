<?php

namespace Nurun\Bundle\RhBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'required' => true))
            ->add('description', 'text', array(
                'required' => true))
            ->add('role',  'choice', array(
                'choices'   =>  array(
                    'User'      =>  'ROLE_USER',
                    'RDP'       =>  'ROLE_RDP',
                    'Admin'     =>  'ROLE_ADMIN',
                    'Gestion'   =>  'ROLE_GESTION',
                    'Root'      =>  'ROLE_ROOT'
                    ),
                'choices_as_values' => true,
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'choice_attr'=> array('style'),
                'invalid_message_parameters' => 'Invalid choice'
                )
            )
            ->add('save', 'submit',array(
                'label'=>'enregistrer'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nurun\Bundle\RhBundle\Entity\Action'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nurun_bundle_rhbundle_action';
    }
}
