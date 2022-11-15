<?php

namespace App\Controller;

use App\Entity\Order;
use Stripe\StripeClient;
use App\Service\CartService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    #[Route('/payment/{order}', name: 'payment')]
    public function index(Request $request, CartService $cartService, Order $order): Response
    {
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
                        'description' => $product['product']->getDescription()
                    ]
                ]
            ];
            $stripeCart[] = $stripeElement;
        }

        $stripe = new StripeClient($this->getParameter('stripe_secret_key')); // initialise Stripe avec la clé secrète déclaré dans .env.local

        $stripeSession = $stripe->checkout->sessions->create([ // création de la session paiement Stripe
            'line_items' => $stripeCart,
            'mode' => 'payment',
            'success_url' => 'https://127.0.0.1:8000/payment/' . $order->getId() . '/success',
            'cancel_url' => 'https://127.0.0.1:8000/payment/cancel',
            'payment_method_types' => ['card']
        ]);
        
        return $this->render('payment/index.html.twig', [
            'sessionId' => $stripeSession->id,
            'order' => $order->getId()
        ]);
    }

    #[Route('/payment/{order}/success/', name: 'payment_success')]
    public function success(Request $request, CartService $cartService, Order $order, ManagerRegistry $managerRegistry): Response
    {
        if ($request->headers->get('referer') !== 'https://checkout.stripe.com/') { // vérifier qu'on viens bien de Stripe
            return $this->redirectToRoute('cart');
        }

        $cartService->clear(); // vide le panier quand le paiement est un succès
        
        $order->setPaid(true); // passe la commande à paid->true en faisant un setPaid(true)
        $managerRegistry->getManager()->persist($order);
        $managerRegistry->getManager();

        foreach ($order->getOrderDetails() as $orderDetail) { // gestion des stocks restants en base de données
            $product = $orderDetail->getProduct();
            $product->setQuantity($product->getQuantity() - $orderDetail->getQuantity());
            $managerRegistry->getManager()->persist($product);
        }

        $managerRegistry->getManager()->flush();

        return $this->render('payment/success.html.twig');
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function cancel(Request $request): Response
    {
        if ($request->headers->get('referer') !== 'https://checkout.stripe.com/') { // vérifie qu'on vient bien de Stripe
            return $this->redirectToRoute('cart');
        }
        return $this->render('payment/cancel.html.twig');
    }
}