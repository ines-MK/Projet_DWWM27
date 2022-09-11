<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $category = new Category();
        $category->setName('Taille');
        $category->setSlug('taille');
        $manager->persist($category);

        $manager->flush();

        $faker = Factory::create();

        $categories = $manager->getRepository 
        (Category::class)->findAll();

        $slugger = new AsciiSlugger();

        for ($i = 1; $i <= 3; $i++) {
            $product = new Product();
            $product->SetName($faker->text(35));
            $product->SetSlug(strtolower($slugger->slug($product->getName())));
            $product->setPicture($faker->text(45));
            $product->setPrice($faker->randomFloat(2, 1, 150)); // Je lui demande de me générer un nombre à deux decimal entre 1 et 150
            $product->setDescription($faker->text(1000));
            
            $index = array_rand($categories, 1); 
            $category = $categories[$index];
            $product->setCategory($category); 

            $manager->persist($product);
        }

        $manager->flush();
    }
}
