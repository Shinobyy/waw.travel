<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Roadtrip;
use App\Entity\Vehicles;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoadtripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('cover_image', FileType::class, [
                'label' => 'Images de couverture',
                'multiple' => false,
                'mapped' => false,
                'required' => false,
                'attr' => ['accept' => 'image/*'],
            ])
            ->add('vehicle', EntityType::class, [
                'class' => Vehicles::class,
                'choice_label' => 'type',
                'choices' => $options['vehicles']
            ])
            ->add('checkpoints', CollectionType::class, [
                'entry_type' => CheckpointType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'required' => false, // Rend les checkpoints optionnels
                'label' => 'Checkpoints',
                'entry_options' => [
                    'label' => false,
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Soumettre',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Roadtrip::class,
            'vehicles' => [],
        ]);
    }
}
