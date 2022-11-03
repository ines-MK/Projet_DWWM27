<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
use App\Form\CartValidationType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function index(CartService $cartService, ProductRepository $productRepository): Response
    {
            return $this->render('cart/index.html.twig', [
                'cart' => $cartService->getCart(),
                'total' => $cartService->getTotal()
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add(CartService $cartService, ProductRepository $productRepository, int $id, Request $request): Response
    {
        $cartService->add($id);
        $this->AddFlash('success', 'L\'article a bien été ajouté au panier');
        if ($request->headers->get('referer') === 'https://127.0.0.1:8000/cart') {
            return $this->redirectToRoute('cart');
        }
        return $this->redirectToRoute('products'); // redirection vers page produits
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove(CartService $cartService, ProductRepository $productRepository, int $id): Response
    {
        $cartService->remove($id);
        $this->AddFlash('success', 'L\'article a bien été supprimé du panier');
        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/delete/{id}', name: 'cart_delete')]
    public function delete(CartService $cartService, ProductRepository $productRepository, int $id): Response
    {
        $cartService->delete($id);
        $this->AddFlash('success', 'Le produit a bien été supprimé.');
        return $this->redirectToRoute('cart');
    }



    #[Route('/cart/clear', name: 'cart_clear')]
    public function clear(CartService $cartService): Response
    {
        $cartService->clear();
        $this->AddFlash('success', 'Le panier a bien été vidé.');
        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/validation', name:'cart_validation')]
    public function validate(CartService $cartService):Response 
    {
        $cartValidationForm =$this->createForm(CartValidationType::class); // formulaire permettant de gérer les infos du style addresse de livraison/facturation etc.

        // récup donnée du form 
        // génere donnée en bdd
        // traite le transporteur comme un produit (+ ajoute au  panier)
        // redirection vers /payment

        return $this->render('cart/validation.html.twig', [
            'cart' => $cartService->getCart(),
            'total' => $cartService->getTotal(),
            'cartValidationForm' => $cartValidationForm->createView()
        ]);
    }

    public function getNbProducts(CartService $cartService) : Response // CETTE METHODE N'EST PAS LIE A UNE ROUTE, ELLE PERMET SEULEMENT D'AFFICHER LA QUANTITE DE PRODUITS PRESENT DANS LE PANIER
    {
        return $this->render('cart/nbProducts.html.twig', [
            'nbProducts' => $cartService->getNbProducts()
        ]);
    }
}