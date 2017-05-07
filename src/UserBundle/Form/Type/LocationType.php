<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('street', TextType::class, array(
                'label' => 'profile.location.street'
            ))
            ->add('postal_code', TextType::class, array(
                'label' => 'profile.location.postal_code',
                'addon' => 'icon',
                'addon_content' => 'fa fa-envelope-o'
            ))
            ->add('city', TextType::class, array(
                'label' => 'profile.location.city',
                'addon' => 'icon',
                'addon_content' => 'fa fa-map-o'
            ))
            ->add('country', TextType::class, array(
                'label' => 'profile.location.country'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Location',
            'translation_domain' => 'Profile'
        ));
    }
}
