<?php
namespace UserBundle\Form\Type;

use AdminBundle\Form\Type\AddressType;
use AppBundle\Entity\PaymentMethod;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentMethodType extends AbstractType implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('method', ChoiceType::class, array(
                'label' => 'Méthode',
                'required' => true,
                'choices' => PaymentMethod::METHOD_CHOICES,
            ))
            ->add('iban', TextType::class, array(
                'label' => 'IBAN',
                'required' => true
            ))
            ->add('bic', TextType::class, array(
                'label' => 'BIC'
            ))
            ->add('location', AddressType::class, array(
                'label' => 'Addresse de facturation',
                'required' => false
            ))
            ->add('company', TextType::class, array(
                'label' => 'Nom de l\'entreprise',
                'required' => false
            ))
            ->add('name', TextType::class, array(
                'label' => 'Nom et prénom',
                'required' => false
            ))
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PaymentMethod',
            'translation_domain' => 'Settings',
        ));
    }
}