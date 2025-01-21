<?php

namespace App\Form;

use App\Entity\Checkpoint;
use App\Entity\Roadtrip;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CheckpointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'required' => false,
                'attr' => [
                    'class' => 'my-2 w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary-red focus:border-primary-red transition-colors',
                    'placeholder' => 'Nom du lieu',
                ],
            ])
            ->add('google_maps_coordinates', null, [
                'required' => false,
                'attr' => [
                    'class' => 'my-2 w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary-red focus:border-primary-red transition-colors',
                    'placeholder' => 'Coordonnées GPS (sélectionnez sur la carte)',
                    'readonly' => 'readonly',
                ],
            ])
            ->add('arrival_date', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'label' => 'Date d\'arrivée',
                'html5' => true,
                'required' => false,
                'attr' => [
                    'class' => 'my-2 w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary-red focus:border-primary-red transition-colors',
                ],
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\LessThan([
                        'value' => new \DateTimeImmutable('+100 years'),
                        'message' => 'La date doit être dans un intervalle raisonnable.',
                    ]),
                    new Assert\GreaterThan([
                        'value' => new \DateTimeImmutable('-100 years'),
                        'message' => 'La date doit être dans un intervalle raisonnable.',
                    ]),
                ],
            ])
            ->add('departure_date', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'label' => 'Date de départ',
                'html5' => true,
                'required' => false,
                'attr' => [
                    'class' => 'my-2 w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary-red focus:border-primary-red transition-colors',
                ],
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\LessThan([
                        'value' => new \DateTimeImmutable('+100 years'),
                        'message' => 'La date doit être dans un intervalle raisonnable.',
                    ]),
                    new Assert\GreaterThan([
                        'value' => new \DateTimeImmutable('-100 years'),
                        'message' => 'La date doit être dans un intervalle raisonnable.',
                    ]),
                ],
            ]);
            // ->add('created_at', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('updated_at', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('roadtrip_id', EntityType::class, [
            //     'class' => Roadtrip::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('roadtrip', EntityType::class, [
            //     'class' => Roadtrip::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Checkpoint::class,
        ]);
    }
}
