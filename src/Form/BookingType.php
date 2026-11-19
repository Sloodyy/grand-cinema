<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Seat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('seats', EntityType::class, [
            'class' => Seat::class,
            'choices' => $options['available_seats'],
            'choice_label' => fn (Seat $seat) =>
                $seat->getRowLabel() . ' - Place ' . $seat->getSeatNumber(),
            'multiple' => true,
            'expanded' => true,
            'mapped' => false,
            'label' => 'Choisissez vos places',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'available_seats' => [],
        ]);

        $resolver->setAllowedTypes('available_seats', 'array');
    }
}
