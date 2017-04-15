<?php

namespace Nurun\Bundle\RhBundle\Form;

use Nurun\Bundle\UserBundle\NurunUserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class NiveauType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('niveau', 'text')
            ->add('description', 'text',
                array('required' => false))
//            ->add('domaine', 'text',
//                array('required' => false))
            ->add('domaine', ChoiceType::class, array(
                'choices'  => array(
                    'LANGUE' => 'Langue étrangère',
                    'MOBILITE' => 'Mobilité',
                    'EXPERTISE' => 'Expertise',
                    'COMPETENCE' => 'Compétence'
                )))
            ->add('save', 'submit');

    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nurun\Bundle\RhBundle\Entity\Niveau'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nurun_bundle_rhbundle_niveau';
    }
}
