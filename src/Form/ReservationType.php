<?php

namespace App\Form;

use App\Entity\Apartment;
use App\Entity\Client;
use App\Entity\Reservation;
use App\Entity\ReservationState;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('startAt', DateType::class, ['label' => 'Date d\'arrivée'])
      ->add('endAt', DateType::class, ['label' => 'Date de départ'])
      ->add('client', EntityType::class, [
          'class' => Client::class,
          'choice_label' => 'getFullName',
          'label' => 'Client'
      ])
      ->add('nbOfPersons', NumberType::class, ['label' => 'Nombre de personnes'])
      ->add('price', TextType::class, ['label' => 'Prix'])
      ->add('apartment', EntityType::class, [
          'class' => Apartment::class,
          'choice_label' => 'name',
          'label' => 'Appartement'
      ])
      ->add('state', EntityType::class, [
          'class' => ReservationState::class,
          'choice_label' => 'name',
          'label' => 'Statut'
      ])
      ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
        'data_class' => Reservation::class,
    ]);
  }
}
