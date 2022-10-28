<?php

namespace App\Service;

use Stripe\StripeClient;

class PaymentService
{
    private $cartService;
    private $stripe;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
        $this->stripe = new StripeClient('sk_test_51LxUsiHFhbncnsKS5ltLuDwdLldZNgd3k3so7nJFBqivcz40sPsT7quE3Po6eYoo0ZpblvbQv5RPOmt72DnOGZJK00g5WrL1xL');
    }

    public function create(): string
    {
        $cart = $this->cartService->get();
        $items = [];
        foreach ($cart['elements'] as $productId => $element)
        {
            $items[] = [
                'quantity' => $element['quantity'],
                'price_data' => [
                    'currency' =>'EUR', 
                    'unit_amount' => $element['product']->getPrice() * 100,
                    'product_data' => [
                        'name' => $element['product']->getName(),
                        'description' => $element['product']->getDescription(),
                    ]
                ]
            ];
            $stripeCart[] = $items;
        }

        $protocol = $_SERVER['HTTPS'] ? 'https' : 'http';
        $host = $_SERVER['SERVER_NAME'];
        $successUrl = $protocol . '://' . $host . '/payment/success/{CHECKOUT_SESSION_ID}';
        $failureUrl = $protocol . '://' . $host . '/payment/failure/{CHECKOUT_SESSION_ID}';

        $session = $this->stripe->checkout->sessions->create([
            'success_url' => $successUrl,
            'cancel_url' => $failureUrl,
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => $items
        ]);

        return $session->id;
    }

}