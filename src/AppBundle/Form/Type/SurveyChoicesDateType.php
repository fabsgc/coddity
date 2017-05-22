<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SurveyChoicesDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('choices', CollectionType::class, array(
                'label' => false,
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'required' => false,
                'attr' => array(
                    'class' => 'with_default',
                    'data-number-to-add' => 1
                ),
                'prototype' => true,
                'entry_options'  => array(
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Date'
                    ],
                    'required' => false
                ),
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Suivant',
                'attr' => array('class' => 'btn-primary'),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SurveyChoices'
        ));
    }
}