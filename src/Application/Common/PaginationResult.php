<?php

declare(strict_types=1);

namespace App\Application\Common;

use JsonSerializable;

class PaginationResult implements JsonSerializable
{
    public function __construct(
        private array $items,
        private int $page,
        private int $perPage,
        private int $pagesCount,
        private int $total,
    )
    {
        
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getPagesCount(): int
    {
        return $this->pagesCount;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function jsonSerialize(): mixed {
        return [
            'items' => $this->items,
            'page' => $this->page,
            'per_page' => $this->perPage,
            'pages_count' => $this->pagesCount,
            'total' => $this->total,
        ];
    }
}
