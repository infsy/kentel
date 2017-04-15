<?php

namespace Nurun\Bundle\RhBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Nurun\Bundle\RhBundle\Entity\Client;

class ConseillerMandatType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifiantMandat', EntityType::class, array(
                'class'         => 'NurunRhBundle:Client',
                'placeholder'   => '',
                'query_builder' => function (\Nurun\Bundle\RhBundle\Entity\ClientRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.acronyme', 'ASC');
                }
            ))
            ->add('dateDebut', 'genemu_jquerydate', array('widget' => 'single_text'))
            ->add('dateFin', 'genemu_jquerydate', array('widget' => 'single_text'))
            ->add('statutAffectation', 'entity', array(
                'class' => 'NurunRhBundle:StatutAffectation',
                // 'property' => 'acronyme',
                'multiple' => false,
                'required' => true
            ))
            ->add('pourcentage', 'text')
            ->add('commentaire','textarea', array(
                'required' => false
            ))
            ->add('save',      'submit')
        ;

        $conseiller = $builder->getData()->getConseiller();
        if($conseiller){
            $builder->add('conseiller', 'entity', array(
                'class'         => 'NurunRhBundle:conseiller',
                'placeholder'   => '',
                'choices'       => array($conseiller),
                'disabled'      => true
            ));
        }
        else{
            $builder->add('conseiller', 'entity', array(
                'class' => 'NurunRhBundle:Conseiller',
                    'query_builder' => function(\Nurun\Bundle\RhBundle\Entity\ConseillerRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->where('u.deletedAt is null')
                            ->orderBy('u.prenom', 'ASC');
                        },
                    'required' => true
                )
            );
        }


        $formModifier = function (FormInterface $form, Client $client = null) {
            $mandats = null === $client ? array() : $client->getMandats();

            $form->add('mandat', EntityType::class, array(
                'class'         => 'NurunRhBundle:Mandat',
                'placeholder'   => '',
                'choices'       => $mandats,
            ));
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $form = $event->getForm();                

                $formModifier($form, $data->getIdentifiantMandat());
            }
        );

        $builder->get('identifiantMandat')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($formModifier) {
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
            'data_class' => 'Nurun\Bundle\RhBundle\Entity\ConseillerMandat'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'nurun_bundle_rhbundle_conseillerMandat';
    }
}
