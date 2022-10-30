<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, SluggerInterface $slugger, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $contact = $form->getData(); // ici je récupère les données du formulaire
            $email = (new TemplatedEmail())
            ->from(new Address($contact['email'], $contact['prenom'] . ' ' . $contact['nom'])) // expediteur
            ->to(new Address('elrizana.contact@gmail.com')) 
            ->subject('El Rizana - demande de contact - ' . $contact['sujet']) // objet
            ->htmlTemplate('contact/emailcontact.html.twig') // chemin du template email
            ->context([ // passe les données du form au template
                'prenom' => $contact['prenom'],
                'nom' => $contact['nom'],
                'emailAddress' => $contact['email'], // "email" est un nom réservé donc je met "emailAddress" à la place
                'sujet' => $contact['sujet'],
                'message' => $contact['message']
            ]);
            if ($contact['piece_jointe'] !== null) { // vérifie si le champs PJ est vide
                $originalFileName = pathinfo($contact['piece_jointe']->getClientOriginalName(), PATHINFO_FILENAME); // récup nom original du fichier
                $safeFileName = $slugger->slug($originalFileName); // inclu nom du fichier dans l'url
                $newFileName = $safeFileName . '.' . $contact['piece_jointe']->guessExtension(); // renomme fichier pour lui donner une extension
                $email->attachFromPath($contact['piece_jointe']->getPathName(), $newFileName); // attache PJ au corps du mail 
            }
            $mailer->send($email); // envoi de l'email
            $this->addFlash('success', 'Votre message a bien été envoyé');
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView()
        ]);
    }
}
