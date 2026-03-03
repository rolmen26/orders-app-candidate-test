<?php

namespace Src\Orders\Application\UseCases\Products;

use Illuminate\Support\Facades\DB;

class CreateProductUseCase
{
    public function execute(string $sku, string $name, float $price, int $stock): int
    {
        $result = DB::select(
            'CALL sp_create_product(?, ?, ?, ?)',
            [$sku, $name, $price, $stock]
        );

        return $result[0]->id;
    }
}

