<?php

namespace Nurun\Bundle\RhBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConseillerCompetenceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('competence', 'entity', array(
                'class' => 'NurunRhBundle:Competence',
                'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\CompetenceRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.competence', 'ASC');
                },
                'property' => 'competence',
                'multiple' => false,
                'required' => true))
            ->add('niveau', 'entity', array(
                'class' => 'NurunRhBundle:Niveau',
                'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\NiveauRepository $er) {
                    return $er->createQueryBuilder('n')
                        ->where('n.domaine = ?1')
                        ->orderBy('n.niveau', 'ASC')
                        ->setParameter(1,'EXPERTISE');
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
            'data_class' => 'Nurun\Bundle\RhBundle\Entity\ConseillerCompetence'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nurun_bundle_rhbundle_conseillerCompetence';
    }
}
