<?php

namespace Src\Orders\Application\UseCases\Products;

use Illuminate\Support\Facades\DB;

class GetProductByIdUseCase
{
    public function execute(int $productId): ?object
    {
        $result = DB::select('CALL sp_get_product_by_id(?)', [$productId]);

        return $result[0] ?? null;
    }
}

