<?php

namespace Src\Orders\Domain\Interfaces;

interface ProductsRepositoryInterface
{
    public function paginate(
        ?string $search,
        ?string $sortBy,
        ?string $sortOrder,
        int $page,
        int $perPage
    ): array;

    public function findById(int $id): ?array;

    public function create(string $sku, string $name, string $price, int $stock): int;

    public function update(int $id, string $sku, string $name, string $price, int $stock): int;

    public function delete(int $id): int;

    public function checkStock(int $productId, int $quantity): ?array;

    public function decreaseStock(int $productId, int $quantity): int;
}
