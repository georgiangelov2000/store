<?php

namespace App\Requests;

use Symfony\Component\HttpFoundation\Request;

class GetProductRequest
{
    private int $offset;
    private int $limit;
    private ?string $search;
    private ?string $orderColumn;
    private ?string $orderDir;

    public function __construct(Request $request)
    {
        $this->offset = (int) $request->query->get('start', 0);
        $this->limit = (int) $request->query->get('length', 10);
        $this->search = $request->query->get('search', '');

        // Map column index to actual field name
        $columnIndex = (int) $request->query->get('order_column', 0);
        $validColumns = ['id', 'unitPrice']; // Ensure valid fields

        // Convert index to column name, default to "id"
        $this->orderColumn = $validColumns[$columnIndex] ?? 'id';

        // Validate sorting direction
        $this->orderDir = strtolower($request->query->get('order_dir', 'asc'));
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
            'search' => $this->search,
            'order_column' => $this->orderColumn,
            'order_dir' => $this->orderDir,
        ];
    }
    
}