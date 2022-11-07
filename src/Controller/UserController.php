<?php

namespace App\Controller;

use App\Entity\User;
use DateTimeImmutable;
use App\Form\RegistrationFormType;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        $addresses = $userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'user_name' => 'UserController',
            'addresses' => $addresses  
        ]);
    }

    #[Route('/user/addresses/{user_id}', name: 'user_address')]
    public function userAddress(AddressRepository $addressRepository, $user_id): Response
    {
        
        $addresses = $addressRepository->findBy([$user_id]);
        if($addresses)

        return $this->render('address/userAddress.html.twig', [
            'addresses' => $addresses  
        ]);
    }

    #[Route('/admin/users', name: 'admin_users')]
    public function userListAdmin(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/userListAdmin.html.twig', [
            'users' => $users
    ]);
    }

    #[Route('/admin/user/create', name:'user_create')]
    public function create(Request $request, ManagerRegistry $managerRegistry, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User(); // création d'un nouvel utilisateur
        $userForm = $this->createForm(RegistrationFormType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) { // traitement des données (detecte si le form à été envoyé et que les données sont valide)

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $userForm->get('plainPassword')->getData()
                )
            );

            $user->setCreatedAt(new DateTimeImmutable);

            $manager = $managerRegistry->getManager();
            $manager->persist($user); // prépare utilisateur à envoi
            $manager->flush(); // envoi en BDD

            $this->addFlash('success', 'L\'utilisateur a bien été créé'); // msg succès
            return $this->redirectToRoute('admin_users');

        }

        return $this->render('user/create.html.twig', [
            'userForm' => $userForm->createView()
        ]);
    }

    #[Route('/admin/user/update/{id}', name:'user_update')]
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

    #[Route('/admin/user/delete/{id}', name:'user_delete')]
    public function delete(User $user, ManagerRegistry $managerRegistry): Response
    {
        $manager = $managerRegistry->getManager();
        $manager->remove($user); // supprime le produit
        $manager->flush();

        $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.'); // msg de succès
        return $this->redirectToRoute('admin_users');
    }
}
