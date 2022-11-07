<?php

namespace App\Controller;

use Stripe\StripeClient;
use App\Service\CartService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'payment')]
    public function index(Request $request, CartService $cartService): Response
    {
        // dd($request->headers->get('referer'));
        if ($request->headers->get('referer') !== 'https://127.0.0.1:8000/cart/validation') {
            return $this->redirectToRoute('cart');
        }

        $sessionCart = $cartService->getCart(); // récup panier en session
        $stripeCart = []; // initialise panier Stripe (qui sera envoyé à Stripe)

        foreach ($sessionCart as $product) { // transforme le panier session en panier Stripe
            $stripeElement = [
                'quantity' => $product['quantity'],
                'price_data' => [
                    'currency' => 'EUR',
                    'unit_amount' => $product['product']->getPrice() * 100,
                    'product_data' => [
                        'name' => $product['product']->getName(),
                        // 'description' => $product['product']->getDescription(),
                        // 'images' => [
                        //     'https://127.0.0.1:8000/public/img/product/' . $product['product']->getImg1()
                        // ]
                    ]
                ]
            ];
            $stripeCart[] = $stripeElement;
        }

        $stripe = new StripeClient($this->getParameter('stripe_secret_key')); // initialise Stripe avec la clé secrète déclaré dans .env.local

        $stripeSession = $stripe->checkout->sessions->create([ // création de la session paiement Stripe
            'line_items' => $stripeCart,
            'mode' => 'payment',
            'success_url' => 'https://127.0.0.1:8000/payment/success',
            'cancel_url' => 'https://127.0.0.1:8000/payment/cancel',
            'payment_method_types' => ['card']
        ]);
        
        return $this->render('payment/index.html.twig', [
            'sessionId' => $stripeSession->id
        ]);
    }

    #[Route('/payment/success/', name: 'payment_success')]
    public function success(Request $request, CartService $cartService): Response
    {
        if ($request->headers->get('referer') !== 'https://checkout.stripe.com/') { // vérifier qu'on viens bien de Stripe
            return $this->redirectToRoute('cart');
        }
        $cartService->clear(); // vide le panier quand le paiement est un succès
        // passe la commande à paid->true en faisant un setPaid(true)
        return $this->render('payment/success.html.twig');
    }

    #[Route('/payment/cancel/', name: 'payment_cancel')]
    public function cancel(Request $request, CartService $cartService): Response
    {
        // vérifier qu'on viens bien de Stripe

        return $this->render('payment/cancel.html.twig');
    }
}
//         $paymentRequest = $paymentRequestRepository->findOneBy([
//             'stripeSessionId' => $stripeSessionId
//         ]);
//         if (!$paymentRequest)
//         {
//             return $this->redirectToRoute('cart');
//         }

//         $paymentRequest->setValidated(true);
//         $paymentRequest->setPaidAt(new DateTimeImmutable());

//         $entityManager->flush();

//         $cartService->clear();

//         
//     

    // #[Route('/payment/cancel/{stripeSessionId}', name: 'payment_cancel')]
    // public function cancel(string $stripeSessionId, PaymentRequestRepository $paymentRequestRepository, EntityManagerInterface $entityManager): Response
    // {
    //     $paymentRequest = $paymentRequestRepository->findOneBy([
    //         'stripeSessionId' => $stripeSessionId
    //     ]);
    //     if (!$paymentRequest)
    //     {
    //         return $this->redirectToRoute('cart');
    //     }

    //     $entityManager->remove($paymentRequest);
    //     $entityManager->flush();

    //     return $this->render('payment/failure.html.twig');
    // }
// }
