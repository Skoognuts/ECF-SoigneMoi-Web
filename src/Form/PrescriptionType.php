<?php

namespace App\Form;

use App\Entity\Prescription;
use App\Entity\Stay;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('medication', TextType::class, [
                'required' => true,
                'mapped' => false,
                'label' => 'Médication',
                'attr' => [
                    'class' => 'form-text-input required'
                ]
            ])
            ->add('quantity', TextType::class, [
                'required' => true,
                'mapped' => false,
                'label' => 'Posologie',
                'attr' => [
                    'class' => 'form-text-input required'
                ]
            ])
            ->add('date_from', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'form-date-input',
                ],
                'label' => 'Du'
            ])
            ->add('date_to', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'form-date-input',
                ],
                'label' => 'Au'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prescription::class,
        ]);
    }
}
