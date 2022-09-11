<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    #[Route('/home', name: 'home')] // quand l'url "/" (donc vide) sera demandée, la méthode index sera exécutée
    public function index(ProductRepository $productRepository): Response
    {
           $product = $productRepository->findBy([], ['name' => 'DESC', 'id' => 'DESC'], 8); // Fait un order By du createdAt en DESC et si il ont la meme date on fait un tri de l'id décroissant avec une limite de 8 produit => affiche les 8 derniers produits par id

        return $this->render('home/index.html.twig', [ // demande à Twig d'afficher le template home/index.html.twig
            'products' => $product, // en lui passant des infos
        ]);
    }

}
