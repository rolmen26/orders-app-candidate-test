<?php

namespace Src\Orders\Application\UseCases\Products;

use Illuminate\Support\Facades\DB;

class GetProductsUseCase
{
    public function execute(
        ?string $search = '',
        ?string $sortBy = 'id',
        ?string $sortOrder = 'ASC',
        int $page = 1,
        int $perPage = 15
    ): array {
        $pdo = DB::connection()->getPdo();

        $stmt = $pdo->prepare('CALL sp_get_products(?, ?, ?, ?, ?)');
        $stmt->execute([$search, $sortBy, $sortOrder, $page, $perPage]);

        $totalResult = $stmt->fetch(\PDO::FETCH_OBJ);
        $total = $totalResult->total ?? 0;

        $stmt->nextRowset();

        $products = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $stmt->closeCursor();

        return [
            'data' => $products,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => ceil($total / $perPage)
        ];
    }
}

