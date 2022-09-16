<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    #[Route('/', name: 'home')] // quand l'url "/" (donc vide) sera demandée, la méthode index sera exécutée
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy([], ['id' => 'DESC'], 3);
        // dd($products);

        return $this->render('home/index.html.twig', [ // demande à Twig d'afficher le template home/index.html.twig
            'products' => $products,
        ]);
    }
}