<?php

namespace App\Requests;

use Symfony\Component\HttpFoundation\Request;

class GetOrderRequest
{
    private int $offset;
    private int $limit;
    private ?string $search;
    private ?string $orderColumn;
    private ?string $orderDir;

    private ?string $status;

    public function __construct(Request $request)
    {
        $this->offset = (int) $request->query->get('start', 0);
        $this->limit = (int) $request->query->get('length', 10);
        $this->search = $request->query->get('search', '');
        $this->status = $request->query->get('status','');

        $this->orderDir = $request->query->get('order_dir', 'asc');
        $this->orderColumn = $request->query->get('order_column');

    }

    /**
     * Get validated filters.
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            'offset' => $this->offset,
            'limit' => $this->limit,
            'status' => $this->status,
            'search' => $this->search,
            'order_column' => $this->orderColumn,
            'order_dir' => $this->orderDir,
        ];
    }
}