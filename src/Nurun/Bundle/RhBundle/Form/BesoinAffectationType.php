<?php

namespace Nurun\Bundle\RhBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

use Nurun\Bundle\RhBundle\Entity\Client;

class BesoinAffectationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('typeBesoin', 'entity', array(
                'class' => 'NurunRhBundle:TypeBesoin',
                'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\TypeBesoinRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.type', 'ASC');
                },
                'required' => true))
            ->add('client', 'entity', array(
                'class' => 'NurunRhBundle:Client',
                'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\ClientRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.acronyme', 'ASC');
                },
                'placeholder' => '',
                'required' => true))
            ->add('dateDebut', 'genemu_jquerydate', array('widget' => 'single_text'))
            ->add('dateFin', 'genemu_jquerydate', array('widget' => 'single_text'))
            ->add('dateRequise', 'genemu_jquerydate', array('widget' => 'single_text'))
            ->add('prioriteBesoin', 'entity', array(
                'class' => 'NurunRhBundle:PrioriteBesoin',
                'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\PrioriteBesoinRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.priorite', 'ASC');
                },
                'required' => true))
            ->add('contexte', 'textarea',
                array('required' => false))
            ->add('profil', 'entity', array(
                'class' => 'NurunRhBundle:ProfilConseiller',
                'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\ProfilConseillerRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.profil', 'ASC');
                },
                'required' => true))
            ->add('niveauCompetence', 'entity', array(
                'class' => 'NurunRhBundle:Niveau',
                'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\NiveauRepository $er) {
                    return $er->createQueryBuilder('n')
                        ->where('n.domaine = ?1')
                        ->orderBy('n.niveau', 'ASC')
                        ->setParameter(1,'COMPETENCE');
                },
                'required' => true))
            ->add('niveauMobilite', 'entity', array(
                'class' => 'NurunRhBundle:Niveau',
                'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\NiveauRepository $er) {
                    return $er->createQueryBuilder('n')
                        ->where('n.domaine = ?1')
                        ->orderBy('n.niveau', 'ASC')
                        ->setParameter(1,'MOBILITE');
                },
                'required' => true))
            ->add('niveauLangue', 'entity', array(
                'class' => 'NurunRhBundle:Niveau',
                'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\NiveauRepository $er) {
                    return $er->createQueryBuilder('n')
                        ->where('n.domaine = ?1')
                        ->orderBy('n.niveau', 'ASC')
                        ->setParameter(1,'LANGUE');
                },
                'required' => true))
            ->add('commentaire', 'textarea', array(
                'required' => false))
            ->add('propositionAffectation', 'entity', array(
                'class' => 'NurunRhBundle:Conseiller',
                 'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\ConseillerRepository $er) {
                return $er->createQueryBuilder('u')
                    ->orderBy('u.prenom', 'ASC');
                 },
                 'required' => false))
            ->add('sourceAffectation', 'entity', array(
                'class' => 'NurunRhBundle:SourceAffectation',
                'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\SourceAffectationRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.source', 'ASC');
                },
                'required' => false))
            ->add('statutAffectation', 'entity', array(
                'class' => 'NurunRhBundle:StatutAffectation',
                 'multiple' => false,
                 'required' => true))
            ->add('penalite', 'checkbox', array(
                'required' => false))
            ->add('budget', 'textarea', array(
                'required' => false))
            ->add('save',      'submit')
            ;

        $formModifier = function (FormInterface $form, Client $client = null) {
            $mandats = null === $client ? array() : $client->getMandats();

            $form->add('mandat', 'entity', array(
                'class'         => 'NurunRhBundle:Mandat',
                'placeholder'   => '',
                'choices'       => $mandats
            ));
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();

                $formModifier($event->getForm(), $data->getClient());
            }
        );

        $builder->get('client')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $client = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $client);
            }
        );
    }
    
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nurun\Bundle\RhBundle\Entity\BesoinAffectation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nurun_bundle_rhbundle_besoinAffectation';
    }
}
