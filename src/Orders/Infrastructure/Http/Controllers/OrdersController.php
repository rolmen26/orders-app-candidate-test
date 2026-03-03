<?php

namespace Src\Orders\Infrastructure\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Src\Orders\Application\UseCases\Orders\CreateOrderUseCase;
use Src\Orders\Application\UseCases\Orders\GetOrdersUseCase;
use Src\Orders\Application\UseCases\Orders\GetOrderByIdUseCase;

class OrdersController extends Controller
{
    public function __construct(
        private readonly CreateOrderUseCase  $createOrderUseCase,
        private readonly GetOrdersUseCase    $getOrdersUseCase,
        private readonly GetOrderByIdUseCase $getOrderByIdUseCase
    )
    {
    }

    public function index(Request $request)
    {
        try {
            $orders = $this->getOrdersUseCase->execute(
                dateFrom: $request->query('desde'),
                dateTo: $request->query('hasta'),
                minTotal: $request->query('min_total') ? (float)$request->query('min_total') : null
            );

            return response()->json([
                'data' => $orders
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error fetching orders',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $result = $this->getOrderByIdUseCase->execute($id);

            if (!$result['order']) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error fetching order',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|min:1',
                'items.*.quantity' => 'required|integer|min:1'
            ]);

            // Get authenticated user ID (or use a default for testing)
            $userId = Auth::id() ?? 1;

            $orderId = $this->createOrderUseCase->execute(
                userId: $userId,
                items: $validated['items']
            );

            return response()->json([
                'message' => 'Order created successfully',
                'order_id' => $orderId
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error creating order',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
