<?php

namespace Nurun\Bundle\RhBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConseillerCertificationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('certification', 'entity', array(
                'class' => 'NurunRhBundle:Certification',
                'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\CertificationRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.acronyme IS NOT NULL')
                        ->orderBy('c.acronyme', 'ASC');
                },
                // 'choice_label' => 'acronyme',
                'multiple' => false,
                'required' => true))
            ->add('date', 'genemu_jquerydate', array('widget' => 'single_text'))

            // ->add('niveau', 'entity', array(
            //     'class' => 'NurunRhBundle:Niveau',
            //     'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\NiveauRepository $er) {
            //         return $er->createQueryBuilder('n')
            //             ->where('n.domaine = ?1')
            //             ->orderBy('n.niveau', 'ASC')
            //             ->setParameter(1,'EXPERTISE');
            //     },
            //     'required' => true))
             ->add('save',      'submit')
            ;
    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nurun\Bundle\RhBundle\Entity\ConseillerCertification'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nurun_bundle_rhbundle_conseillerCertification';
    }
}
