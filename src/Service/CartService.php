<?php

namespace App\Service; // sert à définir ou se trouve la class

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    protected $requestStack;
    protected $productRepository;

    public function __construct(RequestStack $requestStack, ProductRepository $productRepository) {
        $this->requestStack = $requestStack;
        $this->productRepository = $productRepository;
    }


    public function add(int $id): void // cette methode fait exactement la même chose que celle du controlleur mais sans le "return"
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function remove(int $id): void
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        if (!empty($cart[$id])) { 
            if ($cart[$id] > 1) { 
                $cart[$id]--; 
            }
            else {
                unset($cart[$id]);
            }
        }
        $this->requestStack->getSession()->set('cart', $cart); // retourne le panier vidé 
    }

    public function delete(int $id): void
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }
        $this->requestStack->getSession()->set('cart', $cart);
    }


    public function clear(): void
    {
        $this->requestStack->getSession()->remove('cart');
    }
        
    public function getCart(): array 
    {
        $sessionCart = $this->requestStack->getSession()->get('cart', []);
        $cart = [];
        foreach ($sessionCart as $id => $quantity) {
            $product = $this->productRepository->find($id);
            $element = [
                'product' => $product,
                'quantity' => $quantity
            ];
            $cart[] = $element;
        }
        return $cart;
    }

    public function getTotal(): float
    {
        $sessionCart = $this->requestStack->getSession()->get('cart', []);
        $total = 0;
        foreach ($sessionCart as $id => $quantity) {
            $product = $this->productRepository->find($id);
            $total += $product->getPrice() * $quantity;
        }
        return $total;
    }

    public function getNbProducts(): int
    {
        $sessionCart = $this->requestStack->getSession()->get('cart', []);
        $nb = 0; // initialisation nb de produits qui vaut 0
        foreach ($sessionCart as $id => $quantity) { // je boucle dans mon panier
            $nb += $quantity;
        }
        return $nb;
    }
}
