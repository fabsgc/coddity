<?php

namespace UserBundle\Form\Type;

use AppBundle\Form\Type\DatepickerType;
use AppBundle\Form\Type\Registration\LocationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TitleChoicesType::class, array(
                'label' => 'Civilité *',
            ))
            ->add('birthDate', BirthdayType::class, array(
                'label' => 'Date de naissance'
            ))
            ->add('first_name', TextType::class, array(
                'label' => 'Prénom *',
                'addon' => 'text',
                'addon_content' => 'Prénom *'
            ))
            ->add('last_name', TextType::class, array(
                'label' => 'Nom *',
                'addon' => 'text',
                'addon_content' => 'Nom *'
            ))
            ->add('email', EmailType::class, array(
                'label' => 'E-mail *',
                'disabled' => true
            ))
            ->add('phone_number', TextType::class, array(
                'label' => 'Numéro de téléphone'
            ))
            ->add('location', LocationType::class, array(
                'label' => 'Adresse',
                'required' => false
            ))
            ->add('pictureUpload', FileType::class, array(
                'label' => false,
                'required' => false
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Enregistrer'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'cascade_validation' => true
        ));
    }
}
