<?php

namespace App\Form;

use App\Entity\Carrier;
use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartValidationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('billing_address', EntityType::class, 
            [
                'class' => Address::class,
                'choice_label' => 'address'
            ])
            ->add('delivery_address', EntityType::class, 
            [
                'class' => Address::class,
                'choice_label' => 'address'
            ])
            ->add('carrier', EntityType::class, 
            [
                'class' => Carrier::class,
                'choice_label' => function(Carrier $carrier) {
                    return $carrier->getName() . ' (' . $carrier->getPrice() . ' €)';
                }
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
