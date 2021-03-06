<?php
namespace AdminBundle\Form\Type;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
                'label' => 'Nom d\'utilisateur',
            ))
            ->add('email', TextType::class, array(
                'label' => 'Email',
            ))
            ->add('plainPassword', TextType::class, array(
                'label' => 'Mot de passe',
                'required' => false,
            ))
            ->add('enabled', CheckboxType::class, array(
                'label' => 'Compte activé ?',
                'required' => false
            ))
            ->add(
                'roles', 'choice', [
                'choices' => ['ROLE_ADMIN' => 'Administrateur', 'ROLE_USER' => 'Utilisateur'],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Statuts'
            ])
            ->add('submit', SubmitType::class, array(
                'label' => 'Enregistrer',
                'attr' => array('class' => 'btn-primary'),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }
}
