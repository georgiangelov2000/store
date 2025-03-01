<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }


    public function findBySku(string $sku): ?Product
    {
        return $this->findOneBy(['sku' => $sku]);
    }

 
    public function findAllProducts(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.sku', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function createProduct(string $sku, string $name, float $unitPrice, ?int $specialQuantity = null, ?float $specialPrice = null): Product
    {
        $product = new Product();
        $product->setSku($sku);
        $product->setName($name);
        $product->setUnitPrice($unitPrice);
        $product->setSpecialQuantity($specialQuantity);
        $product->setSpecialPrice($specialPrice);

        $this->_em->persist($product);
        $this->_em->flush();

        return $product;
    }
}