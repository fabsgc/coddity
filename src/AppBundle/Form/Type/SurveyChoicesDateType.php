<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SurveyChoicesDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('choices', CollectionType::class, [
                'label' => false,
                'entry_type' => DateType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'required' => false,
                'attr' => [
                    'class' => 'with_default',
                    'data-number-to-add' => 1
                ],
                'prototype' => true,
                'entry_options'  => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Date'
                    ],
                    'required' => false,
                    'widget' => 'single_text'
                ],
            ])
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