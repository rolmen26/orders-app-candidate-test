<?php

namespace Src\Orders\Application\UseCases\Orders;

use Illuminate\Support\Facades\DB;
use PDO;

class GetOrderByIdUseCase
{
    public function execute(int $orderId): array
    {
        $pdo = DB::connection()->getPdo();

        $stmt = $pdo->prepare('CALL sp_get_order_by_id(?)');
        $stmt->execute([$orderId]);

        $order = $stmt->fetch(PDO::FETCH_OBJ);

        $stmt->nextRowset();

        $items = $stmt->fetchAll(PDO::FETCH_OBJ);

        $stmt->closeCursor();

        return [
            'order' => $order ?: null,
            'items' => $items
        ];
    }
}

