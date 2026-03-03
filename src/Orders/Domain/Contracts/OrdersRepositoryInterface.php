<?php

namespace Src\Orders\Domain\Interfaces;

interface OrdersRepositoryInterface
{
    public function create(int $userId, array $items): int;

    public function list(?string $dateFrom, ?string $dateTo, ?string $minTotal): array;

    public function findById(int $orderId): array;
}
