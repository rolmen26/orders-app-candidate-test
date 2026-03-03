<?php

namespace Src\Orders\Application\UseCases\Products;

use Src\Orders\Domain\Contracts\ProductsRepositoryInterface;

class GetProductByIdUseCase
{
    public function __construct(
        private readonly ProductsRepositoryInterface $productsRepository
    ) {
    }

    public function execute(int $productId): ?object
    {
        $product = $this->productsRepository->findById($productId);

        return $product ? (object)$product : null;
    }
}

