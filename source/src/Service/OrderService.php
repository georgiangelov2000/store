<?php

namespace App\Service;

use App\Builders\API\OrderBuilder;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\BaseService;

/**
 * Class OrderService
 *
 * This service handles operations related to orders, such as retrieving orders with filters,
 * retrieving order details, and related functionality.
 *
 * @package App\Service
 */

class OrderService extends BaseService
{

    private EntityManagerInterface $entityManager;
    private OrderRepository $orderRepository;

    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository)
    {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Retrieves a list of orders based on the provided filters, including pagination and sorting.
     *
     * @param array $filters An array of filters (e.g., status, date range).
     *
     * @return array Contains `totalOrders`, `filteredOrders`, and `data` (the list of filtered orders).
     */
    public function getData(array $filters): array
    {
        $orderBuilder = new OrderBuilder($this->entityManager, $filters);
        $queryBuilder = $orderBuilder->applyFilters()->applySorting()->applyPagination()->getQueryBuilder();
        $orders = $queryBuilder->getQuery()->getResult();

        $totalOrders = $this->entityManager->getRepository(Order::class)->count([]);

        return [
            "totalOrders" => $totalOrders,
            "filteredOrders" => count($orders),
            "data" => array_map(fn($order) => [
                'id' => $order->getId(),
                'status' => $order->getStatusLabel(),
                'total_price' => $order->getTotalPrice(),
            ], $orders)
        ];
    }

    /**
     * Retrieves the items for a given order by its ID.
     *
     * @param int $orderId The ID of the order.
     *
     * @return array An array containing the order item details.
     *
     * @throws \Exception If the order does not exist.
     */
    public function getOrderItems(int $orderId): array
    {
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            throw new \Exception("Order not found.");
        }

        $items = $order->getItems();

        return array_map(function ($item) {
            return [
                'id' => $item->getId(),
                'product' => $item->getProduct()->getName(),
                'sku' => $item->getProduct()->getSku(),
                'quantity' => $item->getQuantity(),
                'price' => $item->getPrice()
            ];
        }, $items->toArray());
    }
}