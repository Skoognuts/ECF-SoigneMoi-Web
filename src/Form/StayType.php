<?php

namespace App\Form;

use App\Entity\Stay;
use App\Entity\Specialty;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reason', TextType::class, [
                'required' => true,
                'label' => 'Motif',
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
            ->add('specialty', EntityType::class, [
                'required' => true,
                'mapped' => false,
                'class' => Specialty::class,
                'label' => 'Specialité',
                'placeholder' => 'Choisir une spécialté',
                'attr' => [
                    'class' => 'form-text-input required'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stay::class,
        ]);
    }
}
