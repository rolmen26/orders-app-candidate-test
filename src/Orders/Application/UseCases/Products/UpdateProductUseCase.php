<?php

namespace Src\Orders\Application\UseCases\Products;

use Src\Orders\Domain\Contracts\ProductsRepositoryInterface;

class UpdateProductUseCase
{
    public function __construct(
        private readonly ProductsRepositoryInterface $productsRepository
    ) {
    }

    public function execute(int $productId, string $sku, string $name, float $price, int $stock): int
    {
        return $this->productsRepository->update($productId, $sku, $name, (string)$price, $stock);
    }
}

