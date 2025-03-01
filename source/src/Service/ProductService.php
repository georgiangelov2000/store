<?php

namespace App\Service;

use App\Builders\API\ProductBuilder;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ProductService
 *
 * This service is responsible for retrieving and processing product-related data, 
 * including support for searching, pagination, and formatting.
 *
 * @package App\Service
 */
class ProductService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getData(int $offset, int $limit, string $search): array
    {
        $productBuilder = new ProductBuilder($this->entityManager);
        $queryBuilder = $productBuilder
            ->applySearch($search)
            ->applyPagination($offset, $limit)
            ->getQueryBuilder();

        $products = $queryBuilder->getQuery()->getResult();
        $totalProducts = $this->entityManager->getRepository(Product::class)->count([]);

        // 🔹 Format data
        $data = array_map(fn($product) => [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'sku' => $product->getSku(),
            'price' => $product->getUnitPrice(),
        ], $products);

        return [
            "totalProducts" => $totalProducts,
            "filteredProducts" => count($products),
            "data" => $data
        ];
    }
}