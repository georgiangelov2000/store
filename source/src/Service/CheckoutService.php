<?php

namespace App\Service;

use App\Repository\ProductRepository;
use App\Entity\Order;
use App\Entity\OrderItem;
use Doctrine\ORM\EntityManagerInterface;
use Exception;


/**
 * Class CheckoutService
 *
 * This service handles the processing of orders, including managing transactions
 * and calculating prices with potential special pricing rules.
 *
 * @package App\Service
 */
class CheckoutService
{
    private ProductRepository $productRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager)
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Processes an order by calculating prices for items and persisting an Order and OrderItems.
     *
     * @param array $items An associative array where the keys are product SKUs and the values are quantities.
     *
     * @return Order The processed Order entity.
     *
     * @throws Exception If any error occurs during order processing or product lookup.
     */

    public function processOrder(array $items): Order
    {
        $this->entityManager->beginTransaction(); // Start transaction
        try {
            $order = new Order();
            $totalPrice = 0;
            foreach ($items as $sku => $quantity) {
                $product = $this->productRepository->findBySku($sku);

                if (!$product) {
                    throw new Exception("Product with SKU $sku not found");
                }
    
                $price = $this->calculatePrice($product->getUnitPrice(), $product->getSpecialQuantity(), $product->getSpecialPrice(), $quantity);
                
                $totalPrice += $price;
    
                $orderItem = new OrderItem();
                $orderItem->setOrder($order);
                $orderItem->setProduct($product);
                $orderItem->setQuantity($quantity);
                $orderItem->setPrice($price);
    
                $this->entityManager->persist($orderItem);
            }
    
            $order->setTotalPrice($totalPrice);
            $this->entityManager->persist($order);
            $this->entityManager->flush();
    
            $this->entityManager->commit(); // Commit transaction
            return $order;
        } catch (Exception $e) {
            $this->entityManager->rollback(); // Rollback transaction if error
            throw new Exception("Order processing failed: " . $e->getMessage());
        }
    }
    

    /**
     * Calculates the price for a product, considering special pricing rules.
     *
     * @param float $unitPrice The unit price of the product.
     * @param int|null $specialQuantity The quantity required for the special price to apply (if exists).
     * @param float|null $specialPrice The special price for the given quantity (if exists).
     * @param int $quantity The quantity of the purchased product.
     *
     * @return float The total calculated price for the given quantity of products.
     */
    private function calculatePrice(float $unitPrice, ?int $specialQuantity, ?float $specialPrice, int $quantity): float
    {
        if ($specialQuantity && $specialPrice) {
            $specialSetCount = intdiv($quantity, $specialQuantity);
            $remaining = $quantity % $specialQuantity;
            return ($specialSetCount * $specialPrice) + ($remaining * $unitPrice);
        }
        return $quantity * $unitPrice;
    }
}