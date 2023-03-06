<?php

namespace App\Form;

use App\Entity\Apartment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApartmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom de l\'appartement'])
            ->add('address', TextType::class, ['label' => 'Adresse de l\'appartement'])
            ->add('complementAddress', TextType::class, ['label' => 'Complement de l\'appartement'])
            ->add('postcode', NumberType::class, ['label' => 'Code postal'])
            ->add('city', TextType::class, ['label' => 'Ville'])
            ->add('localisationDescription', TextareaType::class, ['label' => 'Description de la localisation', 'required' => false])
            ->add('type', ChoiceType::class, [
                    'choices' => [
                        'Appartement' => 'Apartment',
                        'Maison' => 'Home',
                    ],
                    'label' => 'Type de logement'
                ])
            ->add('capacity', TextType::class, ['label' => 'Capacité d\'accueil'])
            ->add('surface', TextType::class, ['label' => 'Surface'])
            ->add('pets', ChoiceType::class, [
                    'choices' => [
                        'Non' => 'no',
                        'Oui' => 'yes',
                    ],
                    'label' => 'Animaux autorisés'
                ])
            ->add('numberOfRooms', TextType::class, ['label' => 'Nombre de chambres'])
            ->add('numberOfBeds', TextType::class, ['label' => 'Nombre de lits'])
            ->add('firstCode', TextType::class, ['label' => 'Code 1', 'required' => false])
            ->add('secondCode', TextType::class, ['label' => 'Code 2', 'required' => false])
            ->add('thirdCode', TextType::class, ['label' => 'Code 3', 'required' => false])
            ->add('color', ColorType::class, ['label' => 'Couleur'])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Apartment::class
        ]);
    }
}
