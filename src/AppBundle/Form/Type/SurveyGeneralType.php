<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SurveyGeneralType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Nom',
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Description',
            ))
            ->add(
                'type', ChoiceType::class, [
                'choices' => ['CHOICE' => 'Choix', 'DATE' => 'Date'],
                'label' => 'Type de choix'
            ])
            ->add('multiple', CheckboxType::class, array(
                'label' => 'Choix Multiple ?',
                'required' => false
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
            'data_class' => 'AppBundle\Entity\SurveyGeneral'
        ));
    }
}