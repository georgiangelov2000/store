<?php

namespace App\Builders\API;

use App\Builders\API\BaseBuilder;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order;

class OrderBuilder extends BaseBuilder
{
    public function __construct(EntityManagerInterface $entityManager, array $filters = [])
    {
        parent::__construct($entityManager, Order::class, 'o', $filters);
    }

    /**
     * Apply filters dynamically based on stored properties.
     *
     * @return self
     */
    public function applyFilters(): self
    {

        // ID Filter
        if (isset($this->filters['id'])) {
            $this->queryBuilder->andWhere("{$this->alias}.id IN (:ids)")
                ->setParameter('ids', explode(',', $this->filters['id']));
        }

        // Search Filter (Match Entity Field Names)
        if (isset($this->filters['search'])) {
            $searchTerm = '%' . $this->filters['search'] . '%';
            $this->queryBuilder->andWhere("{$this->alias}.totalPrice LIKE :search OR {$this->alias}.id LIKE :search")
                ->setParameter('search', $searchTerm);
        }

        // Status Filter
        if (isset($this->filters['status'])) {
            $this->queryBuilder->andWhere("{$this->alias}.status IN (:status)")
                ->setParameter('status', (array) $this->filters['status']);
        }

        return $this;
    }
}