<?php

namespace Src\Orders\Application\UseCases\Products;

use Src\Orders\Domain\Contracts\ProductsRepositoryInterface;

class CheckProductStockUseCase
{
    public function __construct(
        private readonly ProductsRepositoryInterface $productsRepository
    ) {
    }

    public function execute(int $productId, int $quantity): ?object
    {
        $result = $this->productsRepository->checkStock($productId, $quantity);

        return $result ? (object)$result : null;
    }
}

