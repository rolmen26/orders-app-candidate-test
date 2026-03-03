<?php

namespace Src\Orders\Application\UseCases\Orders;

use Src\Orders\Domain\Contracts\OrdersRepositoryInterface;

class CreateOrderUseCase
{
    public function __construct(
        private readonly OrdersRepositoryInterface $ordersRepository
    ) {
    }

    public function execute(int $userId, array $items): int
    {
        return $this->ordersRepository->create($userId, $items);
    }
}

