<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'products')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/{slug}', name:'product_detail')]
    public function detail($slug, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['slug' => $slug]);
        return $this->render('product/detail.html.twig', [
            'product' => $product
        ]);
        // dd($product);
    }

    #[Route ('/admin/products', name: 'admin_products')]
    public function productListAdmin(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('product/productListAdmin.html.twig', [
            'products' => $products
    ]);
    }

    // #[Route('/admin/product/create', name: 'product_create')]
    // public function create(Request $request, ManagerRegistry $managerRegistry): Response
    // {
    //     $product = new Product(); 
    //     $form = $this->createForm(ProductType::class, $product); // création d'un formulaire avec en paramètre le nouveau produit
    //     $form->handleRequest($request); // gestionnaire de requêtes HTTP

    //     if ($form->isSubmitted() && $form->isValid()) { // vérifie si le formulaire a été soumis et est valide

    //         $products = $productRepository->findAll(); // récupère tous les produits en base de données
    //         $productNames = []; // initialise un tableau pour les noms de produits
    //         foreach ($products as $existingProduct) { // pour chaque produit récupéré
    //             $productNames[] = strtolower($existingProduct->getName()); // stocke le nom du produit dans le tableau
    //         }
    //         if (in_array(strtolower($form['name']->getData()), $productNames)) { // vérifie qsi le nom du produit à créé n'est pas déjà utilisé en base de données
    //             $this->addFlash('danger', 'Le produit n\'a pas pu être créé : le nom de produit est déjà utilisé');
    //             return $this->redirectToRoute('admin_products');
    //         }

    //         $infoImg1 = $form['img1']->getData(); // récupère les données du champ img1 du formulaire

    //         if (empty($infoImg1)) { // vérifie la présence de l'image principale dans le formulaire
    //             $this->addFlash('danger', 'Le produit n\'a pas pu être créé : l\'image principale est obligatoire mais n\'a pas été renseignée');
    //             return $this->redirectToRoute('admin_products');
    //         }

    //         $extensionImg1 = $infoImg1->guessExtension(); // récupère l'extension de fichier de l'image 1
    //         $nomImg1 = time() . '-1.' . $extensionImg1; // crée un nom de fichier unique pour l'image 1
    //         $infoImg1->move($this->getParameter('product_image_dir'), $nomImg1); // télécharge le fichier dans le dossier adéquat
    //         $product->setImg1($nomImg1); // définit le nom de l'image à mettre ne base de données

    //         $infoImg2 = $form['img2']->getData();
    //         if ($infoImg2 !== null) {
    //             $extensionImg2 = $infoImg2->guessExtension();
    //             $nomImg2 = time() . '-2.' . $extensionImg2;
    //             $infoImg2->move($this->getParameter('product_image_dir'), $nomImg2);
    //             $product->setImg2($nomImg2);
    //         }

    //         $infoImg3 = $form['img3']->getData();
    //         if ($infoImg3 !== null) {
    //             $extensionImg3 = $infoImg3->guessExtension();
    //             $nomImg3 = time() . '-3.' . $extensionImg3;
    //             $infoImg3->move($this->getParameter('product_image_dir'), $nomImg3);
    //             $product->setImg3($nomImg3);
    //         }

    //         $slugger = new AsciiSlugger();
    //         $product->setSlug(strtolower($slugger->slug($form['name']->getData()))); // génère un slug à partir du titre renseigné dans le formulaire
    //         $product->setCreatedAt(new \DateTimeImmutable());

    //         $manager = $managerRegistry->getManager();
    //         $manager->persist($product);
    //         $manager->flush();

    //         $this->addFlash('success', 'Le produit a bien été créé'); // message de succès
    //         return $this->redirectToRoute('admin_products');
    //     }

    //     return $this->render('product/formAdmin.html.twig', [
    //         'productForm' => $form->createView()
    //     ]);
    // }
}