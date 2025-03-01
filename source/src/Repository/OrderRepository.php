<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findOrderWithItems(int $orderId): ?Order
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.items', 'items')
            ->addSelect('items')
            ->leftJoin('items.product', 'product')
            ->addSelect('product')
            ->where('o.id = :orderId')
            ->setParameter('orderId', $orderId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}