<?php

namespace App\Form;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class ClientAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Client::class,
            'placeholder' => 'Tapez le nom d\'un client',
            'choice_label' => 'getFullName',

            'query_builder' => function(ClientRepository $clientRepository) {
                return $clientRepository->createQueryBuilder('client');
            },
            //'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}
