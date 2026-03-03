<?php

namespace Src\Orders\Application\UseCases\Products;

use Illuminate\Support\Facades\DB;

class DeleteProductUseCase
{
    public function execute(int $productId): int
    {
        $result = DB::select('CALL sp_delete_product(?)', [$productId]);

        return $result[0]->affected_rows;
    }
}

