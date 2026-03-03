<?php

namespace Src\Orders\Application\UseCases\Orders;

use Src\Orders\Domain\Contracts\OrdersRepositoryInterface;

class GetOrderByIdUseCase
{
    public function __construct(
        private readonly OrdersRepositoryInterface $ordersRepository
    ) {
    }

    public function execute(int $orderId): array
    {
        $result = $this->ordersRepository->findById($orderId);
        $order = $result['order'] ?? null;
        $items = $result['items'] ?? [];

        return [
            'order' => $order ? (object)$order : null,
            'items' => array_map(static fn (array $row) => (object)$row, $items),
        ];
    }
}

