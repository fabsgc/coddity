<?php

namespace UserBundle\Form\Type;

use AppBundle\Form\Extension\TextTypeExtension;
use AppBundle\Form\Type\DatepickerType;
use AppBundle\Form\Type\Registration\LocationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfessionalType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
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
                'addon_content' => 'profile.first_name'
            ))
            ->add('last_name', TextType::class, array(
                'label' => 'Nom *',
                'addon' => 'text',
                'addon_content' => 'profile.last_name'
            ))
            ->add('email', EmailType::class, array(
                'label' => 'E-mail *',
                'disabled' => true
            ))
            ->add('language', TextType::class, array(
                'label' => 'Langue *'
            ))
            ->add('phone_number', TextType::class, array(
                'label' => 'Numéro de téléphone',
            ))
            ->add('honorary_type', HonoraryChoicesType::class, array(
                'label' => 'Type d\'honoraires *'
            ))
            ->add('honorary_from', MoneyType::class, array(
                'label' => 'De (en €) *'
            ))
            ->add('honorary_to', MoneyType::class, array(
                'label' => 'Jusqu\'à (en €) *'
            ))
            ->add('profession', TextType::class, array(
                'label' => 'Profession',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Avocat, notaire...'
                ),
            ))
            ->add('specialization', TextType::class, array(

                'label' => 'Spécialisation',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Commerce, successions...'
                ),
            ))
            ->add('educations', CollectionType::class, array(
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
                        'placeholder' => 'Formation'
                    ]
                )
            ))
            ->add('company', TextType::class, array(
                'label' => 'Cabinet',
                'required' => false
            ))
            ->add('companyDescription', TextareaType::class, array(
                'label' => 'Description du cabinet',
                'required' => false,
            ))
            ->add('linkedin', TextType::class, array(
                'label' => 'Page Linkedin',
                'required' => false,
            ))
            ->add('website', TextType::class, array(
                'label' => 'Site Web',
                'required' => false,
            ))
            ->add('location', LocationType::class, array(
                'label' => 'Adresse *',
            ))
            ->add('accessIndication', TextareaType::class, array(
                'label' => 'Indications d\'accès',
                'required' => false,
            ))
            ->add('disabledAccess', CheckboxType::class, array(
                'label' => 'Accès handicapé ?',
                'required' => false,
            ))
            ->add('pictureUpload', FileType::class, array(
                'label' => false,
                'required' => false
            ))
            ->add('paymentMethod', PaymentMethodType::class, array(
                'label' => false,
                'required' => false
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'form.save'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Professional',
            'translation_domain' => 'Profile',
            'cascade_validation' => true,
        ));
    }
}
