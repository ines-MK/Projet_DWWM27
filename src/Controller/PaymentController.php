<?php

namespace App\Controller;

use App\Service\CartService;
use App\Entity\PaymentRequest;
use App\Service\PaymentService;
use App\Repository\PaymentRequestRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'payment')]
    public function index(PaymentService $paymentService, EntityManagerInterface $entityManager): Response
    {
        $sessionId = $paymentService->create();

        $paymentRequest = new PaymentRequest();
        $paymentRequest->setCreatedAt(new DateTimeImmutable());
        $paymentRequest->setStripeSessionId($sessionId);

        $entityManager->persist($paymentRequest);
        $entityManager->flush(); 

        return $this->render('payment/index.html.twig', [
            'sessionId' => $sessionId
        ]);
    }

    #[Route('/payment/success/{stripeSessionId}', name: 'payment_success')]
    public function success(string $stripeSessionId, PaymentRequestRepository $paymentRequestRepository, CartService $cartService, EntityManagerInterface $entityManager): Response
    {
        $paymentRequest = $paymentRequestRepository->findOneBy([
            'stripeSessionId' => $stripeSessionId
        ]);
        if (!$paymentRequest)
        {
            return $this->redirectToRoute('cart');
        }

        $paymentRequest->setValidated(true);
        $paymentRequest->setPaidAt(new DateTimeImmutable());

        $entityManager->flush();

        $cartService->clear();

        return $this->render('payment/success.html.twig');
    }

    #[Route('/payment/failure/{stripeSessionId}', name: 'payment_failure')]
    public function failure(string $stripeSessionId, PaymentRequestRepository $paymentRequestRepository, EntityManagerInterface $entityManager): Response
    {
        $paymentRequest = $paymentRequestRepository->findOneBy([
            'stripeSessionId' => $stripeSessionId
        ]);
        if (!$paymentRequest)
        {
            return $this->redirectToRoute('cart');
        }

        $entityManager->remove($paymentRequest);
        $entityManager->flush();

        return $this->render('payment/failure.html.twig');
    }
}
