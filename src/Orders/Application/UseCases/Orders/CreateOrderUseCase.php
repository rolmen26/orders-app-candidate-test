<?php

namespace Src\Orders\Application\UseCases\Orders;

use Illuminate\Support\Facades\DB;

class CreateOrderUseCase
{
    public function execute(int $userId, array $items): int
    {
        $itemsJson = json_encode($items);

        $result = DB::select(
            'CALL sp_create_order(?, ?)',
            [$userId, $itemsJson]
        );

        return $result[0]->order_id;
    }
}

