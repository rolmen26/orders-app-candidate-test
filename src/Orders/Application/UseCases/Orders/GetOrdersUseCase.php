<?php

namespace Src\Orders\Application\UseCases\Orders;

use Illuminate\Support\Facades\DB;

class GetOrdersUseCase
{
    public function execute(?string $dateFrom = null, ?string $dateTo = null, ?float $minTotal = null): array
    {
        return DB::select(
            'CALL sp_get_orders(?, ?, ?)',
            [$dateFrom, $dateTo, $minTotal]
        );
    }
}

