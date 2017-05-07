<?php
namespace UserBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfessionalSettingsType extends UserType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('meetingTimeLimitMin', ChoiceType::class, array(
                'choices' => [
                    '1' => '1 heure',
                    '2' => '2 heures',
                    '3' => '3 heures',
                    '6' => '6 heures',
                    '12' => '12 heures',
                    '24' => '1 jour',
                    '48' => '2 jours',
                    '72' => '3 jours',
                    '96' => '4 jours'
                ],
                'label_attr' => array('title' => 'Si vous indiquez "1 heure", vos clients peuvent encore prendre un rendez-vous 1 heure avant son début'),
                'label' => 'settings.meetingTimeLimitMin',
            ))
            ->add('meetingTimeLimitMax', ChoiceType::class, array(
                'choices' => [
                    '6' => '6 heures',
                    '12' => '12 heures',
                    '24' => '1 jour',
                    '48' => '2 jours',
                    '72' => '3 jours',
                    '96' => '4 jours',
                    '120' => '5 jours',
                    '144' => '6 jours',
                    '168' => '7 jours'
                ],
                'label_attr' => array('title' => 'Si vous indiquez "7 jours", vos clients peuvent prendre rendez-vous une semaine avant'),
                'label' => 'settings.meetingTimeLimitMax',
            ))
            ->add('meetingDuration', ChoiceType::class, array(
                'choices' => [
                    '15' => '15 minutes',
                    '30' => '30 minutes',
                    '45' => '45 minutes',
                    '60' => '1 heure',
                    '120' => '2 heures',
                    '180' => '3 heures',
                    '240' => '4 heures',
                ],
                'label' => 'settings.meetingDuration',
            ))
            ->add('meetingTimeLimitForCancellation', ChoiceType::class, array(
                'choices' => [
                    '30' => '30 minutes',
                    '60' => '1 heure',
                    '120' => '2 heures',
                    '180' => '3 heures',
                    '240' => '4 heures'
                ],
                'label' => 'settings.meetingTimeLimitForCancellation',
            ))
            ->add('timeAfterMeeting', ChoiceType::class, array(
                'choices' => [
                    '15' => '15 minutes',
                    '30' => '30 minutes',
                    '60' => '1 heure',
                    '120' => '2 heures',
                    '180' => '3 heures',
                    '240' => '4 heures'
                ],
                'label' => 'settings.timeAfterMeeting'
            ))
            ->add('paperBill', CheckboxType::class, array(
                'label' => 'settings.paperBill',
                'required' => false,
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'form.save'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ProfessionalSettings',
            'translation_domain' => 'Settings'
        ));
    }
}