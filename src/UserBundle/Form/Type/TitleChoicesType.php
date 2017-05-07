<?php
namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TitleChoicesType extends AbstractType
{

    private $choices;

    public function __construct(array $choices)
    {
        $this->choices = $choices;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->choices
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}