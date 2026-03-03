<?php

namespace Src\Orders\Application\UseCases\Products;

use Illuminate\Support\Facades\DB;

class UpdateProductUseCase
{
    public function execute(int $productId, string $sku, string $name, float $price, int $stock): int
    {
        $result = DB::select(
            'CALL sp_update_product(?, ?, ?, ?, ?)',
            [$productId, $sku, $name, $price, $stock]
        );

        return $result[0]->affected_rows;
    }
}

