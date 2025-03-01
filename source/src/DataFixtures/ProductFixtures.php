<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            ['A', 'Product A', 50, 3, 130],
            ['B', 'Product B', 30, 2, 45],
            ['C', 'Product C', 20, null, null],
            ['D', 'Product D', 10, null, null]
        ];
        
        foreach ($products as $data) {
            $product = new Product();
            $product->setSku($data[0]);
            $product->setName($data[1]);
            $product->setUnitPrice($data[2]);
            $product->setSpecialQuantity($data[3]);
            $product->setSpecialPrice($data[4]);

            $manager->persist($product);
        }

        $manager->flush();
    }
}