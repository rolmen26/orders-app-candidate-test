<?php

namespace Src\Orders\Application\UseCases\Products;

use Src\Orders\Domain\Contracts\ProductsRepositoryInterface;

class CreateProductUseCase
{
    public function __construct(
        private readonly ProductsRepositoryInterface $productsRepository
    ) {
    }

    public function execute(string $sku, string $name, float $price, int $stock): int
    {
        return $this->productsRepository->create($sku, $name, (string)$price, $stock);
    }
}

