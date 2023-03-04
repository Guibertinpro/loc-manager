<?php

namespace App\Form;

use App\Entity\Apartment;
use App\Entity\Reservation;
use App\Entity\ReservationState;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
      ->add('startAt', DateTimeType::class, [
        'label' => 'Date d\'arrivée',
        'attr' => [
          'class' => 'dateField d-block d-md-flex',
          ],
        ])
      ->add('endAt', DateTimeType::class, [
        'label' => 'Date de départ',
        'attr' => ['class' => 'dateField d-block d-md-flex'],
        ])
      ->add('client', ClientAutocompleteField::class)
      ->add('nbOfAdults', NumberType::class, ['label' => 'Nombre d\'adultes'])
      ->add('nbOfChildren', NumberType::class, ['label' => 'Nombre d\'enfants'])
      ->add('price', TextType::class, ['label' => 'Prix'])
      ->add('arrhes', TextType::class, ['label' => 'Arrhes'])
      ->add('leftToPay', TextType::class, ['label' => 'Solde restant'])
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

    $builder->get('startAt')->addModelTransformer(new CallbackTransformer(
      function ($value) {
        if(!$value) {
          $date = new \DateTime('now');
          $date = $date->setTime(16, 0);
          return $date;
        }
        return $value;
      },
      function ($value) {
        return $value;
      }
    ));

    $builder->get('endAt')->addModelTransformer(new CallbackTransformer(
      function ($value) {
        if(!$value) {
          $date = new \DateTime('now');
          $date = $date->setTime(10, 0);
          return $date;
        }
        return $value;
      },
      function ($value) {
        return $value;
      }
    ));
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Reservation::class,
    ]);
  }
}
