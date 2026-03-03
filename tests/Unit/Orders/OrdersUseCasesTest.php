<?php

namespace Tests\Unit\Orders;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;
use Src\Orders\Application\UseCases\Orders\CreateOrderUseCase;
use Src\Orders\Application\UseCases\Orders\GetOrderByIdUseCase;
use Src\Orders\Application\UseCases\Orders\GetOrdersUseCase;
use Src\Orders\Domain\Contracts\OrdersRepositoryInterface;

class OrdersUseCasesTest extends TestCase
{

    use RefreshDatabase;

    public function test_get_orders_passes_min_total_string_and_maps(): void
    {
        $repository = $this->createMock(OrdersRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('list')
            ->with('2026-03-01', '2026-03-02', '100.5')
            ->willReturn([
                ['id' => 1, 'user_id' => 10, 'total' => '120.00'],
                ['id' => 2, 'user_id' => 11, 'total' => '150.00'],
            ]);

        $useCase = new GetOrdersUseCase($repository);
        $result = $useCase->execute('2026-03-01', '2026-03-02', 100.5);

        $this->assertCount(2, $result);
        $this->assertIsObject($result[0]);
        $this->assertSame(1, $result[0]->id);
        $this->assertSame('120.00', $result[0]->total);
    }

    public function test_get_order_by_id_maps_order_and_items(): void
    {
        $repository = $this->createMock(OrdersRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('findById')
            ->with(77)
            ->willReturn([
                'order' => ['id' => 77, 'total' => '300.00'],
                'items' => [
                    ['id' => 1, 'product_id' => 9, 'quantity' => 2],
                    ['id' => 2, 'product_id' => 10, 'quantity' => 1],
                ],
            ]);

        $useCase = new GetOrderByIdUseCase($repository);
        $result = $useCase->execute(77);

        $this->assertIsObject($result['order']);
        $this->assertSame(77, $result['order']->id);
        $this->assertCount(2, $result['items']);
        $this->assertIsObject($result['items'][0]);
        $this->assertSame(9, $result['items'][0]->product_id);
    }

    public function test_create_order_delegates_to_repository(): void
    {
        $repository = $this->createMock(OrdersRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('create')
            ->with(7, [['product_id' => 3, 'quantity' => 2]])
            ->willReturn(555);

        $useCase = new CreateOrderUseCase($repository);

        $this->assertSame(555, $useCase->execute(7, [['product_id' => 3, 'quantity' => 2]]));
    }
}
