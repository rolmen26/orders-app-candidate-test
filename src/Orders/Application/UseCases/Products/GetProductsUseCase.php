<?php

namespace Src\Orders\Application\UseCases\Products;

use Src\Orders\Domain\Contracts\ProductsRepositoryInterface;

class GetProductsUseCase
{
    public function __construct(
        private readonly ProductsRepositoryInterface $productsRepository
    ) {
    }

    public function execute(
        ?string $search = '',
        ?string $sortBy = 'id',
        ?string $sortOrder = 'ASC',
        int $page = 1,
        int $perPage = 15
    ): array {
        $result = $this->productsRepository->paginate(
            $search,
            $sortBy,
            $sortOrder,
            $page,
            $perPage
        );

        $total = (int)($result['total'] ?? 0);
        $data = array_map(
            static fn (array $row) => (object)$row,
            $result['data'] ?? []
        );

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => $perPage > 0 ? (int)ceil($total / $perPage) : 0,
        ];
    }
}

