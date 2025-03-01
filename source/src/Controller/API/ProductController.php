<?php

namespace App\Controller\API;

use App\Requests\GetProductRequest;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProductController
 *
 * This controller handles API endpoints related to product data. Its main functionality
 * is to return product lists with support for pagination, filtering, and searching.
 *
 * @package App\Controller\API
 */

class ProductController extends AbstractController
{

    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Returns a list of products as a JSON response, supporting pagination and search functionality.
     *
     * @param Request $request The HTTP request object containing query parameters for offset, limit, and search.
     * @param ProductService $productService The service used for retrieving product data.
     *
     * @return JsonResponse The formatted response for the DataTables API, including product data, total count, and filtered count.
     */
    public function getProducts(Request $request): JsonResponse
    {
        $filters = (new GetProductRequest($request))->getFilters();
        $result = $this->productService->getData($filters);

        return $this->json([
            "draw" => $request->query->get('draw', 1),
            "recordsTotal" => $result["totalProducts"],
            "recordsFiltered" => $result["filteredProducts"],
            "data" => $result["data"]
        ],200);
    }
    
    
}