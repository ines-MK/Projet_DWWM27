<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\CartValidationType;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function index(CartService $cartService): Response
    {
        $cart = $cartService->get();
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'cart' => $cart
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add(CartService $cartService, Product $product): Response
    {
        $cartService->add($product);
        $this->addFlash('success', 'L\article a bien été ajouté au panier');
        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove(CartService $cartService, Product $product): Response
    {
        $cartService->remove($product);
        $this->addFlash('success', 'L\article a bien été supprimé du panier');
        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/clear', name: 'cart_clear')]
    public function clear(CartService $cartService): Response
    {
        $cartService->clear();
        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/validation', name:'cart_validation')]
    public function validate(CartService $cartService):Response 
    {
        $cartValidationForm =$this->createForm(CartValidationType::class);
        $cart = $cartService->get();
        return $this->render('cart/validation.html.twig', [
            'cart' => $cart,
            'cartValidationForm' => $cartValidationForm->createView()
        ]);
        
    }
        
}
