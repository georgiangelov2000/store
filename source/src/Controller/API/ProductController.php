<?php

namespace App\Controller\API;

use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{

    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function listProducts(Request $request, ProductService $productService): JsonResponse
    {
        $offset = (int) $request->query->get('start', 0);
        $limit = (int) $request->query->get('length', 10);
        $search = $request->query->get('search', '');
    
        $data = $productService->getData($offset, $limit, $search);
    
        return $this->json([
            "draw" => $request->query->get('draw', 1),
            "recordsTotal" => $data["totalProducts"],
            "recordsFiltered" => $data["filteredProducts"],
            "data" => $data["data"]
        ],200);
    }
    
    
}