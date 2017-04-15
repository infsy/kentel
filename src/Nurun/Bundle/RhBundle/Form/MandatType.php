<?php

namespace Nurun\Bundle\RhBundle\Form;

use Nurun\Bundle\UserBundle\NurunUserBundle\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;

class MandatType extends AbstractType
{
//    private $user;
//    
//    public function __construct( User $user )
//    {
//        $this->user = $user;
//    }
//    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', 'text')
            ->add('identifiant', 'text')
            ->add('secteur', 'text')
            ->add('nbreHeures', 'number', array(
                'precision' => 1,
                'rounding_mode' => IntegerToLocalizedStringTransformer::ROUND_CEILING, 'grouping' => \NumberFormatter::GROUPING_USED
            ))
            ->add('client', 'entity', array(
                'class' => 'NurunRhBundle:Client',
                'query_builder' => function (\Nurun\Bundle\RhBundle\Entity\ClientRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.acronyme', 'ASC');
                },
            ))
            ->add('chargeprojet', 'entity', array(
                'class' => 'NurunRhBundle:Conseiller',
                'query_builder' => function (\Nurun\Bundle\RhBundle\Entity\ConseillerRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.deletedAt is null')
                        ->orderBy('c.prenom', 'ASC');
                },
            ))
            ->add('mandataire', 'entity', array(
                'class' => 'NurunRhBundle:Conseiller',
                'query_builder' => function (\Nurun\Bundle\RhBundle\Entity\ConseillerRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->where('m.deletedAt is null')
                        ->orderBy('m.prenom', 'ASC');
                },
            ))
            ->add('type', 'text')
            ->add('dateFin', 'genemu_jquerydate', array(
                'widget'    => 'single_text',
                'required'  => false
            ))
            ->add('commentaire', 'text', array(
                'required'  => false
            ))
            ->add('save', 'submit');

    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nurun\Bundle\RhBundle\Entity\Mandat'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nurun_bundle_rhbundle_mandat';
    }
}
