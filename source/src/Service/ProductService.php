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

    /**
     * Retrieves products based on filters, pagination, and sorting.
     *
     * @param array $filters Filters for querying products.
     *
     * @return array Returns total products, filtered products count, and product data.
     */

    public function getData(array $filters): array
    {
        $productBuilder = new ProductBuilder($this->entityManager , $filters);
        $queryBuilder = $productBuilder->applyFilters()->applySorting()->applyPagination()->getQueryBuilder();
        $products = $queryBuilder->getQuery()->getResult();

        $totalProducts = $this->entityManager->getRepository(Product::class)->count([]);

        return [
            "totalProducts" => $totalProducts,
            "filteredProducts" => count($products),
            "data" => $products, // Direct return of DTOs, no iteration
        ];
    }
}