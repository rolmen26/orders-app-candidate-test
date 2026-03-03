<?php

namespace Src\Orders\Application\UseCases\Products;

use Src\Orders\Domain\Contracts\ProductsRepositoryInterface;

class DeleteProductUseCase
{
    public function __construct(
        private readonly ProductsRepositoryInterface $productsRepository
    ) {
    }

    public function execute(int $productId): int
    {
        return $this->productsRepository->delete($productId);
    }
}

