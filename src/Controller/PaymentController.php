<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Service\CartService;
use App\Entity\PaymentRequest;
use App\Service\PaymentService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PaymentRequestRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'payment')]
    public function index(Request $request, PaymentService $paymentService, EntityManagerInterface $entityManager): Response
    {
        if ($request->headers->get('referer') !== 'https://127.0.0.1/cart/validation') {
            return $this->redirectToRoute('cart');
        }

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
