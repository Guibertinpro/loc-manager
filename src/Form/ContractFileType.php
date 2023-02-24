<?php

namespace App\Form;

use App\Entity\ContractFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\Dropzone\Form\DropzoneType;

class ContractFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filename', DropzoneType::class, [
                'attr' => [
                    'placeholder' => 'Glisser un fichier ici ou cliquer pour rechercher',
                ],
                'label' => 'Votre fichier (pdf/png/jpg)',

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Seuls les fichiers PDF/JPG/PNG sont acceptés.',
                    ])
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Télécharger'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContractFile::class,
        ]);
    }
}
