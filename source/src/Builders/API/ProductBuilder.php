<?php

namespace App\Builders\API;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductBuilder extends BaseBuilder
{

    public function __construct(EntityManagerInterface $entityManager, array $filters = [])
    {
        parent::__construct($entityManager, Product::class, 'o', $filters);

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
        // Search Filter (Match Entity Field Names including SKU)
        if (isset($this->filters['search'])) {
            $searchTerm = '%' . $this->filters['search'] . '%';
            $this->queryBuilder->andWhere("{$this->alias}.name LIKE :search 
                                            OR {$this->alias}.id LIKE :search 
                                            OR {$this->alias}.sku LIKE :search")
                ->setParameter('search', $searchTerm);
        }
        return $this;
    }
}