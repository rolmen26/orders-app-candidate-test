<?php

namespace Src\Orders\Application\UseCases\Products;

use Illuminate\Support\Facades\DB;

class CheckProductStockUseCase
{
    public function execute(int $productId, int $quantity): ?object
    {
        $result = DB::select(
            'CALL sp_check_product_stock(?, ?)',
            [$productId, $quantity]
        );

        return $result[0] ?? null;
    }
}

