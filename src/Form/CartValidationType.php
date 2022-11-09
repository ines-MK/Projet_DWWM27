<?php

namespace App\Form;

use App\Entity\Address;
use App\Repository\AddressRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartValidationType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('billing_address', EntityType::class, 
            [
                'class' => Address::class,
                'query_builder' => function(AddressRepository $addressRepository) {
                    return $addressRepository->createQueryBuilder('a')
                    ->where('a.user = :val')
                    ->setParameter(':val', $this->security->getUser());
                },
                'choice_label' => function(Address $address) {
                    return $address->getAddress() . ' - ' . $address->getZip() . ' ' . $address->getCity();
                }
            ])
            ->add('delivery_address', EntityType::class, 
            [
                'class' => Address::class,
                'query_builder' => function(AddressRepository $addressRepository) {
                    return $addressRepository->createQueryBuilder('a')
                    ->where('a.user = :val')
                    ->setParameter(':val', $this->security->getUser());
                },
                'choice_label' => function(Address $address) {
                    return $address->getAddress() . ' - ' . $address->getZip() . ' ' . $address->getCity();
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
