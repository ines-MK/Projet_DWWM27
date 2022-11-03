<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'maxLenght' => 255
                ]
            ])
            
            ->add('image', FileType::class, [ // En BDD on stock le nom de l'image pour pouvoir la retrouver mais on envoi pas le fichier direct en BDD
                'required' => false, // je met required à false pour ne pas avoir à la remettre en "update"
                'mapped' => false, // mapped sert dissocier se qu'on recup du form de ce qu'on envoi en BDD
                'help' => 'png, jpg, jpeg, jp2 ou webp - 1 Mo maximum',
                'constraints' => [
                    new Image([
                        'maxSize' => '1M',
                        'maxSizeMessage' => 'Le fichier est trop volumineaux ({{ size }} Mo). Maximum autorisé : {{ limit }} {{ suffix }}.', // Size est une variable permettant de donnée le poid du fichier que le user a uploadé. Variable limit recup limite que j'ai defini avant
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/jp2',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Merci de sélectionner une image au format PNG, JPG, JPEG, JP2 ou WEBP.'
                    ])
                ]
            ])

            ->add('image1', FileType::class, [
                'required' => false,
                'mapped' => false,
                'help' => 'png, jpg, jpeg, jp2 ou webp - 1 Mo maximum',
                'constraints' => [
                    new Image([
                        'maxSize' => '1M',
                        'maxSizeMessage' => 'Le fichier est trop volumineaux ({{ size }} Mo). Maximum autorisé : {{ limit }} {{ suffix }}.', // Size est une variable permettant de donnée le poid du fichier que le user a uploadé. Variable limit recup limite que j'ai defini avant
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/jp2',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Merci de sélectionner une image au format PNG, JPG, JPEG, JP2 ou WEBP.'
                    ])
                ]
            ])

            ->add('image2', FileType::class, [
                'required' => false,
                'mapped' => false,
                'help' => 'png, jpg, jpeg, jp2 ou webp - 1 Mo maximum',
                'constraints' => [
                    new Image([
                        'maxSize' => '1M',
                        'maxSizeMessage' => 'Le fichier est trop volumineaux ({{ size }} Mo). Maximum autorisé : {{ limit }} {{ suffix }}.', // Size est une variable permettant de donnée le poid du fichier que le user a uploadé. Variable limit recup limite que j'ai defini avant
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/jp2',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Merci de sélectionner une image au format PNG, JPG, JPEG, JP2 ou WEBP.'
                    ])
                ]
            ])

            ->add('image3', FileType::class, [
                'required' => false,
                'mapped' => false,
                'help' => 'png, jpg, jpeg, jp2 ou webp - 1 Mo maximum',
                'constraints' => [
                    new Image([
                        'maxSize' => '1M',
                        'maxSizeMessage' => 'Le fichier est trop volumineaux ({{ size }} Mo). Maximum autorisé : {{ limit }} {{ suffix }}.', // Size est une variable permettant de donnée le poid du fichier que le user a uploadé. Variable limit recup limite que j'ai defini avant
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/jp2',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Merci de sélectionner une image au format PNG, JPG, JPEG, JP2 ou WEBP.'
                    ])
                ]
            ])

            ->add('price', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 9999.99,
                    'step' => 0.01
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'maxLenght' => 65535
                ]
            ])
            // ->add('slug')
            ->add('abstract', TextareaType::class, [
                'attr' => [
                    'max' => 255
                ]
            ])
            ->add('quantity', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 99999,
                    'step' => 1 // avance 1 par 1 et évite les float
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
