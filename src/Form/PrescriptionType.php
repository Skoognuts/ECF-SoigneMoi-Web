<?php

namespace App\Form;

use App\Entity\Prescription;
use App\Entity\Stay;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_from', null, [
                'widget' => 'single_text',
            ])
            ->add('date_to', null, [
                'widget' => 'single_text',
            ])
            ->add('medication')
            ->add('stay', EntityType::class, [
                'class' => Stay::class,
                'choice_label' => 'id',
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
