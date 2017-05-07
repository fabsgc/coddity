<?php
namespace AppBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class TextTypeExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return TextType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(array('addon'));
        $resolver->setDefined(array('addon_position'));
        $resolver->setDefined(array('addon_content'));

        $resolver->setDefined(array('selectize'));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['addon'])) {
            $view->vars['addon'] = $options['addon'];
            if (isset($options['addon_position'])) {
                $view->vars['addon_position'] = $options['addon_position'];
            } else {
                $view->vars['addon_position'] = 'left';
            }
            if (isset($options['addon_content'])) {
                $view->vars['addon_content'] = $options['addon_content'];
            } else {
                $view->vars['addon_content'] = '';
            }
        }
        if (isset($options['selectize']) && $options['selectize'] === true) {
            $view->vars['selectize'] = true;
        }
    }
}