<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use App\Repository\AddressRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    // ------------------- Espace personnelle utilisateur ------------------- 
    #[Route('/user', name: 'app_user')]
    public function index(AddressRepository $addressRepository, OrderRepository $orderRepository): Response
    {
        $user_id = $this->getUser(); // recup user connecté
        $addresses = $addressRepository->findBy(['user' => $user_id], ['id' => 'DESC'], 1);
        $orders = $orderRepository->findBy(['user' => $user_id], ['id' => 'DESC'], 3);
        return $this->render('user/index.html.twig', [
            'user_name' => 'UserController',
            'addresses' => $addresses,
            'orders' => $orders
        ]);
    }

    // ------------------- ADMIN | Liste des utilisateur ------------------- 
        #[Route('/admin/users', name: 'admin_users')]
    public function userListAdmin(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/userListAdmin.html.twig', [
            'users' => $users
        ]);
    }

    // ------------------- ADMIN | Modifier un utilisateur ------------------- 
    #[Route('/admin/user/update/{id}', name: 'user_update')]
    public function update(Request $request, ManagerRegistry $managerRegistry, User $user): Response
    {
        $userForm = $this->createForm(RegistrationFormType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            $manager = $managerRegistry->getManager();
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'L\'utilisateur a été modifié avec succès.');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('user/update.html.twig', [
            'userForm' => $userForm->createView()
        ]);
    }

    // ------------------- ADMIN | Supprimer un utilisateur ------------------- 

    #[Route('/user/delete/{id}', name: 'user_delete')]
    public function delete(User $user, ManagerRegistry $managerRegistry): Response
    {
        $manager = $managerRegistry->getManager();
        $manager->remove($user); // supprime le produit
        $manager->flush();

        $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.'); // msg de succès
        return $this->redirectToRoute('admin_users');
    }

    // ------------------- Liste des adresses d'un utilisateur ------------------- 
    #[Route('/user/addresses', name: 'user_addresses')]
    public function userAddress(AddressRepository $addressRepository): Response
    {
        $user_id = $this->getUser(); // recup user connecté
        $addresses = $addressRepository->findBy(['user' => $user_id]);
        return $this->render('address/index.html.twig', [
            'addresses' => $addresses,  
            'user_id' => $user_id
        ]);
    }

    // ------------------- Liste des commandes d'un utilisateur ------------------- 
    #[Route('/user/orders', name: 'user_orders')]
    public function userOrder(OrderRepository $orderRepository): Response
    {
        $user_id = $this->getUser(); // recup user connecté
        $orders = $orderRepository->findBy(['user' => $user_id]);
        return $this->render('order/index.html.twig', [
            'orders' => $orders,  
            'user_id' => $user_id
        ]);
    }
}
