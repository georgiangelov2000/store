<?php

namespace App\Builders\API;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class BaseBuilder
{
    protected QueryBuilder $queryBuilder;
    protected array $filters = [];
    protected string $alias;

    public function __construct(EntityManagerInterface $entityManager, string $entityClass, string $alias, array $filters = [])
    {
        $this->queryBuilder = $entityManager->createQueryBuilder()
            ->select($alias)
            ->from($entityClass, $alias);
        $this->alias = $alias;
        $this->filters = $filters;
    }

    /**
     * Apply sorting to the query.
     *
     * @return self
     */
    public function applySorting(): self
    {
        $orderBy = $this->filters['order_column'];
        $orderDir = $this->filters['order_dir'];

        if (!in_array(strtolower($orderDir), ['asc', 'desc'])) {
            $orderDir = 'asc'; // Default sorting
        }

        $this->queryBuilder->orderBy("{$this->alias}.$orderBy", $orderDir);
        return $this;
    }

    /**
     * Apply pagination to the query.
     *
     * @return self
     */
    public function applyPagination(): self
    {
        $offset = (int) ($this->filters['offset'] ?? 0);
        $limit = (int) ($this->filters['limit'] ?? 10);

        $this->queryBuilder->setFirstResult($offset)->setMaxResults($limit);
        return $this;
 
    }

     /**
     * Get the final query builder.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }
}