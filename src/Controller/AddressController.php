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
    // --------------------- Ajouter une adresse ---------------------
    #[Route('/address/add', name: 'address_add')]
    public function add(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $address = new Address(); // Instenciation d'un objet adresse
        $form = $this->createForm(AddressType::class, $address); // création du formulaire AddresseType
        $form->handleRequest($request); // récup données 

        if ($form->isSubmitted() && $form->isValid()) { 
            $this->getUser(); // récup valeur de user
            $address->setUser($this->getUser()); // affecte la valeur de user à $address

            $manager = $managerRegistry->getManager();
            $manager->persist($address); 
            $manager->flush(); // envoi en BDD

            $this->addFlash('success', 'Votre adresse a bien été ajouté');
            return $this->redirectToRoute('user_addresses');
        }

        return $this->render('address/create.html.twig', [
            'address' => $address,
            'addressForm' => $form->createView()
        ]);
    }

    // --------------------- Modifier une adresse ---------------------
    #[Route('/address/update/{id}', name: 'address_update')]
    public function update(Request $request, ManagerRegistry $managerRegistry, Address $address): Response
    {
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getUser();
            $address->setUser($this->getUser());

            $manager = $managerRegistry->getManager();
            $manager->persist($address); 
            $manager->flush(); // envoi en BDD

            $this->addFlash('success', 'Votre adresse a bien été modifié.');
            return $this->redirectToRoute('user_addresses');
        }

        return $this->render('address/update.html.twig', [
            'address' => $address,
            'addressForm' => $form->createView()
        
        ]);
    }

    // --------------------- Supprimer une adresse ---------------------
    #[Route('/address/delete/{id}', name: 'address_delete')]
    public function delete(Address $address, ManagerRegistry $managerRegistry): Response
    {
        $manager = $managerRegistry->getManager();
        $manager->remove($address); // supprime le produit
        $manager->flush();

        $this->addFlash('success', 'L\'adresse a été supprimé avec succès.'); // msg de succès
        return $this->redirectToRoute('user_addresses');
    }
}