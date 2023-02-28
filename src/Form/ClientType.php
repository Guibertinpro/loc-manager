<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, ['label' => 'Prénom'])
            ->add('lastname', TextType::class, ['label' => 'Nom'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('phone', TextType::class, ['label' => 'Téléphone', 'constraints' => [new Length(['min' => 10, 'max' => 10])]])
            ->add('phoneBis', TextType::class, ['label' => 'Téléphone 2', 'required' => false, 'constraints' => [new Length(['min' => 10, 'max' => 10])]])
            ->add('address', TextType::class, ['label' => 'Adresse'])
            ->add('complementAddress', TextType::class, ['label' => 'Complément d\'adresse', 'required' => false])
            ->add('postcode', NumberType::class, ['label' => 'Code postal'])
            ->add('city', TextType::class, ['label' => 'Ville'])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
