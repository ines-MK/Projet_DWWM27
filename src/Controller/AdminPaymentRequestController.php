<?php

namespace App\Controller;

use App\Entity\PaymentRequest;
use App\Repository\PaymentRequestRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/payment/request')]
class AdminPaymentRequestController extends AbstractController
{
    #[Route('/', name: 'app_admin_payment_request_index', methods: ['GET'])]
    public function index(PaymentRequestRepository $paymentRequestRepository): Response
    {
        return $this->render('admin_payment_request/index.html.twig', [
            'payment_requests' => $paymentRequestRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_payment_request_show', methods: ['GET'])]
    public function show(PaymentRequest $paymentRequest): Response
    {
        return $this->render('admin_payment_request/show.html.twig', [
            'payment_request' => $paymentRequest,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_payment_request_delete', methods: ['POST'])]
    public function delete(Request $request, PaymentRequest $paymentRequest, PaymentRequestRepository $paymentRequestRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paymentRequest->getId(), $request->request->get('_token'))) {
            $paymentRequestRepository->remove($paymentRequest, true);
        }

        return $this->redirectToRoute('app_admin_payment_request_index', [], Response::HTTP_SEE_OTHER);
    }
}
