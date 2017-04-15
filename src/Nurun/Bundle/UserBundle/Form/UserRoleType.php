<?php

namespace Nurun\Bundle\UserBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRoleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles',  'choice', array(
                'choices'   =>  array(
                    'User'      =>  'ROLE_USER',
                    'RDP'       =>  'ROLE_RDP',
                    'Admin'     =>  'ROLE_ADMIN',
                    'Gestion'   =>  'ROLE_GESTION',
                    'Root'      =>  'ROLE_ROOT'
                    ),
                'choices_as_values' => true,
                'multiple' => true,
                'expanded' => true,
                'required' => true,
                'choice_attr'=> array('style'),
                'invalid_message_parameters' => 'Invalid choice'
                )
            )
            ->add('save',   'submit',array('label'=>'enregistrer'))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nurun\Bundle\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nurun_bundle_userbundle_userRole';
    }
}