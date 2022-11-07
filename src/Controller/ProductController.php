<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
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
    }

    #[Route ('/admin/products', name: 'admin_products')]
    public function productListAdmin(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('product/productListAdmin.html.twig', [
            'products' => $products
    ]);
    }

    #[Route('/admin/product/create', name: 'product_create')]
    public function create(Request $request, ProductRepository $productRepository, ManagerRegistry $managerRegistry): Response
    {
        $product = new Product(); // création nouveau produit
        $form = $this->createForm(ProductType::class, $product); // création formulaire avec en paramètre le nouveau produit à alimenter
        $form->handleRequest($request); // Gestionnaire de requêtes HTTP intercepter donnée entré dans le form en chargeant le gestionnaire de requete 
    
        if ($form->isSubmitted() && $form->isValid()) { // traitement des données (detecte si le form à été envoyé et que les données sont valide)
            
            $products = $productRepository->findAll(); // récupere tous les produits en BDD
            $productNames = []; // initialise un tableau pour les noms de produits
            foreach ($products as $exestingProduct) { // pour chaque produit récup en BDD
                $productNames[] = $exestingProduct->getName(); // je stock le nom du produit à l'intérieur du tableau productName 
            }

            if (in_array($form['name']->getData(), $productNames)) { // vérifie que le nom du produit à créer n'est pas déjà utilisé en BDD
                $this->addFlash('danger', 'Le produit n\'a pas pu être créé : le nom de produit est déjà utilisé');
                return $this->redirectToRoute('admin_products');
            }

            $infoImage = $form['image']->getData(); // récup données du champs image dans le form

            if (empty($infoImage)) { // Vérifie la présence de l'image principale dans le form
                $this->addFlash('danger', 'Le produit n\'a pas pu être créé : l\'image principale est obligatoire mais n\'a pas été renseignée');
                return $this->redirectToRoute('admin_products');
            }

            $extensionImage = $infoImage->guessExtension(); // récup l'extension de fichier de l'image
            $nomImage = time() . '-1' . $extensionImage; // crée un nom de fichier unique à l'image uploadé
            $infoImage->move($this->getParameter('product_image_dir'), $nomImage); // télécharge fichier dans le dossier adéquat
            $product->setImage($nomImage); // définit le nom de l'image qu'on stock en BDD 

            $infoImage1 = $form['image1']->getData();
            if($infoImage1 !== null) {
                $extensionImage1 = $infoImage1->guessExtension();
                $nomImage1 = time() . '-2' . $extensionImage1;
                $infoImage1->move($this->getParameter('product_image_dir'), $nomImage1);
                $product->setImage1($nomImage1); 
            }
            $infoImage2 = $form['image2']->getData();
            if($infoImage2 !== null) {
            $extensionImage2 = $infoImage->guessExtension();
            $nomImage2 = time() . '-3' . $extensionImage2;
            $infoImage2->move($this->getParameter('product_image_dir'), $nomImage2);
            $product->setImage2($nomImage2);
            }
            $infoImage3 = $form['image3']->getData();
            if($infoImage3 !== null) {
            $extensionImage3 = $infoImage3->guessExtension();
            $nomImage3 = time() . '-4' . $extensionImage3;
            $infoImage3->move($this->getParameter('product_image_dir'), $nomImage3);
            $product->setImage3($nomImage3);
            }

            $slugger = new AsciiSlugger();  // slug je m'en occupe ici car je l'ai pas fais dans form 
            $product->setSlug(strtolower($slugger->slug($form['name']->getData()))); // je me sert du slugger pour definir le slug de mon produit (il génère un slug à partir du nom renseigné dans le formulaire)
        
            $manager = $managerRegistry->getManager();
            $manager->persist($product); // prépare produit à envoi
            $manager->flush(); // envoi en BDD
            
            $this->addFlash('success', 'Le produit a bien été créé'); // msg succès
            return $this->redirectToRoute('admin_products');
        }
        return $this->render('product/create.html.twig', [
            'productForm' => $form->createView()// au niveau de ma vue j'apl le formulaire 'productForm' qui a pour valeur la vue du formulaire
        ]);
    }

    #[Route('/admin/product/update/{id}', name: 'product_update')]
    public function update(Product $product, ProductRepository $productRepository, Request $request, ManagerRegistry $managerRegistry): Response
    {
        
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $infoImage = $form['image']->getData();

            if ($infoImage !== null ) { // si il y a bien une image entré dans le form
                $oldImageName = $product->getImage(); // recup nom de l'ancienne img
                $oldImagePath = $this->getParameter('product_image_dir') . '/' . $oldImageName; // recup chemin de l'ancienne img (j'ajoute un / pour evite que le nom img soit collé au dossier car il n'y a pas de / dans le parametre)
                if (file_exists($oldImageName)) { // Petite sécu => vérifie qu'il y a bien un fichier à cette addresse avant de le supp
                unlink($oldImagePath); // supprime ancienne img
                }
            
                $extensionImage = $infoImage->guessExtension(); 
                $nomImage = time() . '-1' . $extensionImage; // renomme la nvl image qui est dans le form
                $infoImage->move($this->getParameter('product_image_dir'), $nomImage); // upload de l'img dans dossier public/img/product
                $product->setImage($nomImage); // définit le nom de l'image qu'on stock en BDD 
            }

            $infoImage1 = $form['image1']->getData();
            if ($infoImage1 !== null) {
                $oldImage1Name = $product->getImage1(); // recup nom de l'acienne img 1 en BDD
                if ($oldImage1Name !== null) { // vérifie si il y a une image1 en BDD 
                    $oldImage1Path = $this->getParameter('product_image_dir') . '/' . $oldImage1Name;
                    if (file_exists($oldImage1Path)) {
                        unlink($oldImage1Path);
                    }
                }
                $extensionImage1 = $infoImage1->guessExtension();
                $nomImage1 = time() . '-2' . $extensionImage1;
                $infoImage1->move($this->getParameter('product_image_dir'), $nomImage1);
                $product->setImage1($nomImage1); 
            }

            $infoImage2 = $form['image2']->getData();
            if ($infoImage2 !== null) {
                $oldImage2Name = $product->getImage2(); // recup nom de l'acienne img 1 en BDD
                if ($oldImage2Name !== null) { // vérifie si il y a une image1 en BDD 
                    $oldImage2Path = $this->getParameter('product_image_dir') . '/' . $oldImage2Name;
                    if (file_exists($oldImage2Path)) {
                        unlink($oldImage2Path);
                    }
                }
                $extensionImage2 = $infoImage2->guessExtension();
                $nomImage2 = time() . '-3' . $extensionImage2;
                $infoImage2->move($this->getParameter('product_image_dir'), $nomImage2);
                $product->setImage2($nomImage2); 
            }

            $infoImage3 = $form['image3']->getData();
            if ($infoImage3 !== null) {
                $oldImage3Name = $product->getImage3(); // recup nom de l'acienne img 1 en BDD
                if ($oldImage3Name !== null) { // vérifie si il y a une image1 en BDD 
                    $oldImage3Path = $this->getParameter('product_image_dir') . '/' . $oldImage3Name;
                    if (file_exists($oldImage3Path)) {
                        unlink($oldImage3Path);
                    }
                }
                $extensionImage3 = $infoImage3->guessExtension();
                $nomImage3 = time() . '-4' . $extensionImage3;
                $infoImage3->move($this->getParameter('product_image_dir'), $nomImage3);
                $product->setImage3($nomImage3); 
            }
            
            $slugger = new AsciiSlugger();
            $product->setSlug(strtolower($slugger->slug($form['name']->getData())));
            
            $manager = $managerRegistry->getManager();
            $manager->persist($product);
            $manager->flush();
            $this->addFlash('success', 'Le produit a bien été modifié'); // msg succès
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('product/update.html.twig', [
            'productForm' => $form->createView()
        ]);
    }

    #[Route('/admin/product/delete/{id}', name: 'product_delete')]
    public function delete(Product $product, ManagerRegistry $managerRegistry): Response
    {
        $imagepath = $this->getParameter('product_image_dir') .'/' . $product->getImage();
        if (file_exists($imagepath)) {
            unlink($imagepath);
        }

        if ($product->getImage1() !== null) { // si je recup bien un nom d'image 1 en BDD
            $image1path = $this->getParameter('product_image_dir') .'/' . $product->getImage1();
            if (file_exists($image1path)) {
                unlink($image1path); // alors je procède à l'élimination du sujet
            }
        }

        if ($product->getImage2() !== null) { // si je recup bien un nom d'image 2 en BDD
            $image2path = $this->getParameter('product_image_dir') .'/' . $product->getImage2();
            if (file_exists($image2path)) {
                unlink($image2path); // alors je procède à l'élimination du sujet
            }
        }

        if ($product->getImage3() !== null) { // si je recup bien un nom d'image 3 en BDD
            $image3path = $this->getParameter('product_image_dir') .'/' . $product->getImage3();
            if (file_exists($image3path)) {
                unlink($image3path); // alors je procède à l'élimination du sujet
            }
        }
        
        $manager = $managerRegistry->getManager();
        $manager->remove($product); // supprime le produit
        $manager->flush();
        $this->addFlash('success', 'Le produit a bien été supprimé.'); // msg de succès
        return $this->redirectToRoute('admin_products');// demande a doctrine de supp le produits
    }
}