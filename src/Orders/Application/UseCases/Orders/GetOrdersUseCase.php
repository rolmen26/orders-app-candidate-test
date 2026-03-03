<?php

namespace Src\Orders\Application\UseCases\Orders;

use Src\Orders\Domain\Contracts\OrdersRepositoryInterface;

class GetOrdersUseCase
{
    public function __construct(
        private readonly OrdersRepositoryInterface $ordersRepository
    ) {
    }

    public function execute(?string $dateFrom = null, ?string $dateTo = null, ?float $minTotal = null): array
    {
        $orders = $this->ordersRepository->list(
            $dateFrom,
            $dateTo,
            $minTotal !== null ? (string)$minTotal : null
        );

        return array_map(
            static fn (array $row) => (object)$row,
            $orders
        );
    }
}

