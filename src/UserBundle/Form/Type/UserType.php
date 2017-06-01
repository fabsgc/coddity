<?php

namespace UserBundle\Form\Type;

use AppBundle\Form\Type\DatepickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'label' => false,
                'attr' =>  [
                    'placeholder' => 'Nom d\'utilisateur *'
                ]
            ))
            ->add('email', EmailType::class, array(
                'label' => false,
                'attr' =>  [
                    'placeholder' => 'E-mail *'
                ]
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' =>  'Les mots de passe ne correspondent pas.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options' => array('label' => false, 'attr' => ['placeholder' => 'Mot de passe']),
                'second_options' => array('label' => false, 'attr' => ['placeholder' => 'Confirmation du mot de passe'])
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Inscription'
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
