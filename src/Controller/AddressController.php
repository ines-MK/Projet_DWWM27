<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddressController extends AbstractController
{
    #[Route('/address', name: 'address')]
    public function index(): Response
    {
        return $this->render('address/index.html.twig');
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
}