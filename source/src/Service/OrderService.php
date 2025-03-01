<?php

namespace App\Service;

use App\Builders\API\OrderBuilder;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\BaseService;

class OrderService extends BaseService
{

    private EntityManagerInterface $entityManager;
    private OrderRepository $orderRepository;

    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository)
    {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
    }

    public function getData(array $filters): array
    {
        $orderBuilder = new OrderBuilder($this->entityManager, $filters);
        $queryBuilder = $orderBuilder->applySorting()->applyPagination()->getQueryBuilder();
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