<?php

namespace Src\Orders\Infrastructure\Repositories;

use Src\Orders\Domain\Contracts\ProductsRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProductsRepository implements ProductsRepositoryInterface
{

    public function paginate(
        ?string $search,
        ?string $sortBy,
        ?string $sortOrder,
        int $page,
        int $perPage
    ): array {
        $pdo = DB::connection()->getPdo();

        $stmt = $pdo->prepare("CALL sp_get_products(?,?,?,?,?)");
        $stmt->execute([
            $search ?? '',
            $sortBy ?? 'id',
            $sortOrder ?? 'ASC',
            $page,
            $perPage,
        ]);

        $totalRows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->nextRowset();
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmt->closeCursor();

        return [
            'total' => (int)($totalRows[0]['total'] ?? 0),
            'data' => $products,
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    public function findById(int $id): ?array
    {
        $rows = DB::select("CALL sp_get_product_by_id(?)", [$id]);
        if (!$rows) {
            return null;
        }
        return (array)$rows[0];
    }

    public function create(string $sku, string $name, string $price, int $stock): int
    {
        $rows = DB::select("CALL sp_create_product(?,?,?,?)", [$sku, $name, $price, $stock]);
        return (int)($rows[0]->id ?? 0);
    }

    public function update(int $id, string $sku, string $name, string $price, int $stock): int
    {
        $rows = DB::select("CALL sp_update_product(?,?,?,?,?)", [$id, $sku, $name, $price, $stock]);
        return (int)($rows[0]->affected_rows ?? 0);
    }

    public function delete(int $id): int
    {
        $rows = DB::select("CALL sp_delete_product(?)", [$id]);
        return (int)($rows[0]->affected_rows ?? 0);
    }

    public function checkStock(int $productId, int $quantity): ?array
    {
        $rows = DB::select("CALL sp_check_product_stock(?,?)", [$productId, $quantity]);
        if (!$rows) {
            return null;
        }
        return (array)$rows[0];
    }

    public function decreaseStock(int $productId, int $quantity): int
    {
        $rows = DB::select("CALL sp_update_product_stock(?,?)", [$productId, $quantity]);
        return (int)($rows[0]->affected_rows ?? 0);
    }
}
