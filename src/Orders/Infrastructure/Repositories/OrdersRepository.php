<?php

namespace Src\Orders\Infrastructure\Repositories;

use Src\Orders\Domain\Contracts\OrdersRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrdersRepository implements OrdersRepositoryInterface
{

    public function create(int $userId, array $items): int
    {
        $jsonItems = json_encode($items, JSON_UNESCAPED_UNICODE);

        $rows = DB::select("CALL sp_create_order(?,?)", [$userId, $jsonItems]);
        return (int)($rows[0]->order_id ?? 0);
    }

    public function list(?string $dateFrom, ?string $dateTo, ?string $minTotal): array
    {
        return array_map(
            fn ($r) => (array)$r,
            DB::select("CALL sp_get_orders(?,?,?)", [$dateFrom, $dateTo, $minTotal])
        );
    }

    public function findById(int $orderId): array
    {
        $pdo = DB::connection()->getPdo();

        $stmt = $pdo->prepare("CALL sp_get_order_by_id(?)");
        $stmt->execute([$orderId]);

        $headerRows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->nextRowset();
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmt->closeCursor();

        return [
            'order' => $headerRows[0] ?? null,
            'items' => $items,
        ];
    }
}
