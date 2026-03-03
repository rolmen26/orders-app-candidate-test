<?php

namespace Tests\Unit\Orders;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;
use Src\Orders\Application\UseCases\Products\CheckProductStockUseCase;
use Src\Orders\Application\UseCases\Products\CreateProductUseCase;
use Src\Orders\Application\UseCases\Products\DeleteProductUseCase;
use Src\Orders\Application\UseCases\Products\GetProductByIdUseCase;
use Src\Orders\Application\UseCases\Products\GetProductsUseCase;
use Src\Orders\Application\UseCases\Products\UpdateProductUseCase;
use Src\Orders\Domain\Contracts\ProductsRepositoryInterface;

class ProductsUseCasesTest extends TestCase
{

    use RefreshDatabase;

    public function test_get_products_maps_data_and_pagination(): void
    {
        $repository = $this->createMock(ProductsRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('paginate')
            ->with('term', 'name', 'DESC', 2, 5)
            ->willReturn([
                'data' => [
                    ['id' => 1, 'sku' => 'SKU-1', 'name' => 'First', 'price' => '10.00', 'stock' => 2],
                    ['id' => 2, 'sku' => 'SKU-2', 'name' => 'Second', 'price' => '20.00', 'stock' => 5],
                ],
                'total' => 20,
            ]);

        $useCase = new GetProductsUseCase($repository);
        $result = $useCase->execute('term', 'name', 'DESC', 2, 5);

        $this->assertSame(20, $result['total']);
        $this->assertSame(2, $result['page']);
        $this->assertSame(5, $result['per_page']);
        $this->assertSame(4, $result['last_page']);
        $this->assertCount(2, $result['data']);
        $this->assertIsObject($result['data'][0]);
        $this->assertSame(1, $result['data'][0]->id);
        $this->assertSame('SKU-1', $result['data'][0]->sku);
    }

    public function test_get_product_by_id_returns_null_when_missing(): void
    {
        $repository = $this->createMock(ProductsRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('findById')
            ->with(99)
            ->willReturn(null);

        $useCase = new GetProductByIdUseCase($repository);

        $this->assertNull($useCase->execute(99));
    }

    public function test_create_product_passes_string_price(): void
    {
        $repository = $this->createMock(ProductsRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('create')
            ->with('SKU-9', 'New', '10.5', 7)
            ->willReturn(101);

        $useCase = new CreateProductUseCase($repository);

        $this->assertSame(101, $useCase->execute('SKU-9', 'New', 10.5, 7));
    }

    public function test_update_product_passes_string_price(): void
    {
        $repository = $this->createMock(ProductsRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('update')
            ->with(5, 'SKU-5', 'Updated', '15.75', 4)
            ->willReturn(1);

        $useCase = new UpdateProductUseCase($repository);

        $this->assertSame(1, $useCase->execute(5, 'SKU-5', 'Updated', 15.75, 4));
    }

    public function test_check_product_stock_returns_object_when_found(): void
    {
        $repository = $this->createMock(ProductsRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('checkStock')
            ->with(3, 2)
            ->willReturn(['product_id' => 3, 'stock' => 10]);

        $useCase = new CheckProductStockUseCase($repository);
        $result = $useCase->execute(3, 2);

        $this->assertIsObject($result);
        $this->assertSame(3, $result->product_id);
        $this->assertSame(10, $result->stock);
    }

    public function test_delete_product_delegates_to_repository(): void
    {
        $repository = $this->createMock(ProductsRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('delete')
            ->with(42)
            ->willReturn(1);

        $useCase = new DeleteProductUseCase($repository);

        $this->assertSame(1, $useCase->execute(42));
    }
}
