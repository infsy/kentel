<?php

namespace Nurun\Bundle\UserBundle\Form;

use Exception;
use Doctrine\ORM\EntityRepository;
use Nurun\Bundle\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;


class UserType extends AbstractType
{
    /**
     * @var EntityManager
     */
    public $em;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('username', 'text')
            ->add('email', 'email')
            ->add('roles', 'choice', array(
                    'choices' =>  $this->getRoles(),
                    'choices_as_values' => true,
                    'multiple' => true,
                    'expanded' => true,
                    'required' => true,
                    'choice_attr'=> array('style'),
                    'invalid_message_parameters' => 'Invalid choice'
                )
            )
//            ->add('changePhoto', CheckboxType::class, array('required'=> false ))
            ->add('plainPassword', 'repeated', array(
                    'type' => 'password',
                    'first_options'  => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'))
            )
            ->add('save', SubmitType::class, array('label'=>'enregistrer'))
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
        return 'nurun_userbundle_user';
    }


    /**
     * @return array
     */
    public function getRoles()
    {
        return array(
            'Utilisateur' =>  'ROLE_USER',
            'Administration' =>  'ROLE_ADMIN',
            'Sysadmin' => 'ROLE_ROOT',
            'Gestionnaire' => 'ROLE_GESTION'
            );

    }

}
