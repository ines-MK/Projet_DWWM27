<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class AddressController extends AbstractController
{
    #[Route('/address', name: 'address')]
    public function index(AddressRepository $addressRepository): Response
    {
        $addresses = $addressRepository->findAll();
        return $this->render('address/index.html.twig', [
            'addresses' => $addresses,
        ]);
    }

    #[Route('/address/add', name: 'address_add')]
    public function add(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getUser();
            $address->setUser($this->getUser());

            $manager = $managerRegistry->getManager();
            $manager->persist($address); 
            $manager->flush(); // envoi en BDD

            $this->addFlash('success', 'Votre adresse a bien été ajouté');
            return $this->redirectToRoute('address');
        }

        return $this->render('address/create.html.twig', [
            'address' => $address,
            'addressForm' => $form->createView()
        
        ]);
    }

    // #[Route('/address/user/{id]', name: 'address_user')]
    // public function userAddress(AddressRepository $addressRepository): Response
    // {
    //     $addresses = $addressRepository->findBy();

    //     return $this->render('address/userAddress.html.twig', [
    //         'addresses' => $addresses  
    //     ]);
    // }
}