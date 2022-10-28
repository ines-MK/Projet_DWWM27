<?php

namespace App\Controller;

use App\Entity\PaymentRequest;
use App\Form\PaymentRequestType;
use App\Repository\PaymentRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    #[Route('/new', name: 'app_admin_payment_request_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PaymentRequestRepository $paymentRequestRepository): Response
    {
        $paymentRequest = new PaymentRequest();
        $form = $this->createForm(PaymentRequestType::class, $paymentRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $paymentRequestRepository->add($paymentRequest, true);

            return $this->redirectToRoute('app_admin_payment_request_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_payment_request/new.html.twig', [
            'payment_request' => $paymentRequest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_payment_request_show', methods: ['GET'])]
    public function show(PaymentRequest $paymentRequest): Response
    {
        return $this->render('admin_payment_request/show.html.twig', [
            'payment_request' => $paymentRequest,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_payment_request_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PaymentRequest $paymentRequest, PaymentRequestRepository $paymentRequestRepository): Response
    {
        $form = $this->createForm(PaymentRequestType::class, $paymentRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $paymentRequestRepository->add($paymentRequest, true);

            return $this->redirectToRoute('app_admin_payment_request_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_payment_request/edit.html.twig', [
            'payment_request' => $paymentRequest,
            'form' => $form,
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
