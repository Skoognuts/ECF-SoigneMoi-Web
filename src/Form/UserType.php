<?php

namespace App\Form;

use App\Entity\Specialty;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-text-input required',
                ],
                'label' => 'E-mail'
            ])
            ->add('plainPassword', PasswordType::class, [
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-text-input required'
                ],
                'label' => 'Mot de passe',
            ])
            ->add('firstname', TextType::class, [
                'required' => true,
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'form-text-input required'
                ]
            ])
            ->add('lastname', TextType::class, [
                'required' => true,
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-text-input required'
                ]
            ])
            ->add('registration_number', NumberType::class, [
                'required' => true,
                'label' => 'Matricule',
                'attr' => [
                    'class' => 'form-text-input required'
                ]
            ])
            ->add('specialty', EntityType::class, [
                'required' => true,
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
            'data_class' => User::class,
        ]);
    }
}
