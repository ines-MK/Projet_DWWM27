<?php

namespace App\Controller;

use App\Entity\User;
use DateTimeImmutable;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    // -----------------------------------------------> INSCRIPTION 
    #[Route('/register', name: 'register')]
    // ------- implémentation de la méthode register : (permet au user de s'inscrire sur le site)
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response 
    {   
        // Création d'un nouvel utilisateur 
        $user = new User(); 
        // Je déclare la variable form dans laql je met le formulaire d'inscription
        $form = $this->createForm(RegistrationFormType::class, $user);
        // Je demande au formulaire de récup les données entré par le user à partir de la requete HTTP
        $form->handleRequest($request); 

        //On applique seulement si le formulaire est envoyé et est valide (selon contrainte que j'ai mise dans registrationType.php) 
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password (hash le mdp grâce au hashpassword)
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData() // recup mdp non haché
                )
            );

            $user->setCreatedAt(new DateTimeImmutable);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation('verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('elrizana.contact@gmail.com', 'El Rizana'))
                    ->to($user->getEmail())
                    ->subject('Veuillez confirmer votre adresse e-mail')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    // -----------------------------------------------> VERIFICATION EMAIL 
    #[Route('/verify/email', name: 'verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {   
        // refuse l'accès à moins qu'il ne soit accordé EST ENTIÈREMENT AUTHENTIFIÉ
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre adresse e-mail a bien été vérifiée.');

        return $this->redirectToRoute('home');
    }
}
