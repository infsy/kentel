<?php

namespace Nurun\Bundle\RhBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ConseillerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numEmploye', 'integer')
            ->add('prenom', 'text')
            ->add('nom', 'text')
            ->add('courriel', 'text')
            ->add('poste', 'entity', array(
                'class' => 'NurunRhBundle:PosteConseiller',
                 'property' => 'description',
                 'multiple' => false,
                 'required' => false))
            ->add('profil', 'entity', array(
                'class' => 'NurunRhBundle:ProfilConseiller',
                'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\ProfilConseillerRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.profil', 'ASC');
                },
                'property' => 'profil',
                'multiple' => false,
                'required' => false))
            ->add('vicePresidence', 'entity', array(
                'class' => 'NurunRhBundle:VicePresidence',
                 'property' => 'acronyme',
                 'multiple' => false,
                 'required' => false))
            ->add('statut', 'entity', array(
                'class' => 'NurunRhBundle:StatutConseiller',
                 'property' => 'description',
                 'multiple' => false,
                 'required' => true))
            ->add('role', 'entity', array(
                'class' => 'NurunRhBundle:RoleConseiller',
                 'property' => 'description',
                 'multiple' => false,
                 'required' => false))
            ->add('domaine', 'entity', array(
                'class' => 'NurunRhBundle:Domaine',
                 'property' => 'description',
                 'multiple' => false,
                 'required' => false))
            ->add('telephoneNurun', 'text', array(
                'required' => false
            ))
            ->add('telephoneMandat', 'text', array(
                'required' => false
            ))
            ->add('telephoneAutres', 'text', array(
                'required' => false
            ))
        ->add('dateArrivee', 'genemu_jquerydate', array('widget' => 'single_text'))
            ->add('experienceAnnees', 'integer')
            ->add('experienceMois', 'integer')
            ->add('dateFete', 'genemu_jquerydate', array('widget' => 'single_text', 'required' => false))
//            ->add('dateDepart', 'genemu_jquerydate', array('widget' => 'single_text'))
            ->add('actif', 'checkbox')
            ->add('nbreHeures', 'number',  array('precision' => 1, 
                'rounding_mode' => IntegerToLocalizedStringTransformer::ROUND_CEILING, 'grouping' => \NumberFormatter::GROUPING_USED))
////            ->add('diplomes', 'entity', array(
//                'class' => 'NurunRhBundle:Diplome',
//                'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\DiplomeRepository $er) {
//                return $er->createQueryBuilder('d')
//                    ->orderBy('d.description', 'ASC');
//                 },
//                 'multiple' => true,
//                'required' => false))
            ->add('consigne', 'text', array(
                'required' => false
            ))
            ->add('save',      'submit')
            ;
    }
    
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nurun\Bundle\RhBundle\Entity\Conseiller'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nurun_bundle_rhbundle_conseiller';
    }
}
