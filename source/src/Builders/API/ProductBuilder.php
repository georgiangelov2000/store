<?php

namespace App\Builders\API;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class ProductBuilder
{
    private QueryBuilder $queryBuilder;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->queryBuilder = $entityManager->createQueryBuilder()
            ->select('p')
            ->from('App\Entity\Product', 'p');
    }

    public function applySearch(string $search): self
    {
        if (!empty($search)) {
            $this->queryBuilder->andWhere('p.name LIKE :search OR p.sku LIKE :search')
                ->setParameter('search', "%$search%");
        }
        return $this;
    }

    public function applyPagination(int $offset, int $limit): self
    {
        $this->queryBuilder->setFirstResult($offset)->setMaxResults($limit);
        return $this;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }
}