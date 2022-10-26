<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'attr' => [
                    'maxLenght' => 100
                ] 
            ])
            ->add('nom', TextType::class, [
                'attr' => [
                    'maxLenght' => 100
                ] 
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'maxLenght' => 100
                ] 
            ])
            ->add('sujet', ChoiceType::class, [
                'choices' => [
                    '-- Selectionnez votre demande --' => '',
                    'Commande' => 'Commande',
                    'Livraison' => 'Livraison',
                    'Signaler un problème' => 'Problème',
                    'Autre' => 'Autre'
                ]
            ])
            ->add('message', TextareaType::class, [
            'attr' => [
                'minLenght' => 20,
                'maxLenght' => 2000
            ],
            'help' => '2000 caractères maximum'
            ])
            ->add('piece_jointe', FileType::class, [
                'required' => false,
                'help' => 'image ou document PDF',
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'Le fichier est trop volumineux
                        ({{ size }} {{ suffix }}). 
                        La taille maximale autorisée est de {{ limit }} {{ suffix }}.',
                        'mimeTypes' => [
                            'image/*',
                            'application/pdf'
                        ],
                        'mimeTypesMessage' => 'Le type de fichier est invalide ({{ type }}).
                        Les types de fichiers autorisés sont les suivants : {{ types }}.'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
