<?php

namespace Nurun\Bundle\RhBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;

class ConseillerDiplomeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//             ->add('conseiller', 'text', array(
//                 'read_only' => true))   
            ->add('conseiller', 'entity', array(
                'class' => 'NurunRhBundle:Conseiller',
                'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\ConseillerRepository $er) {
                return $er->createQueryBuilder('u')
                    ->orderBy('u.prenom', 'ASC');
                 },
                 'multiple' => false,
                'property' => 'display',
                 'required' => true))
            ->add('diplome', 'entity', array(
                'class' => 'NurunRhBundle:Diplome',
                'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\DiplomeRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.niveau', 'ASC');
                },
                'multiple' => false,
                'property' => 'description',
                'required' => true))
            ->add('date', 'genemu_jquerydate', array('widget' => 'single_text'))
            ->add('save',      'submit')
            ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nurun\Bundle\RhBundle\Entity\ConseillerDiplome'
        ));
    }

    /**
     * @return string
     */
//    public function getName()
//    {
//        return 'nurun_bundle_rhbundle_conseillerRdp';
//    }
}
