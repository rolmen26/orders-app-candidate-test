<?php

namespace Src\Orders\Infrastructure\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Src\Orders\Application\UseCases\Products\GetProductsUseCase;
use Src\Orders\Application\UseCases\Products\GetProductByIdUseCase;
use Src\Orders\Application\UseCases\Products\CreateProductUseCase;
use Src\Orders\Application\UseCases\Products\UpdateProductUseCase;
use Src\Orders\Application\UseCases\Products\DeleteProductUseCase;
use Src\Orders\Application\UseCases\Products\CheckProductStockUseCase;

class ProductsController extends Controller
{
    public function __construct(
        private readonly GetProductsUseCase       $getProductsUseCase,
        private readonly GetProductByIdUseCase    $getProductByIdUseCase,
        private readonly CreateProductUseCase     $createProductUseCase,
        private readonly UpdateProductUseCase     $updateProductUseCase,
        private readonly DeleteProductUseCase     $deleteProductUseCase,
        private readonly CheckProductStockUseCase $checkProductStockUseCase
    )
    {
    }

    public function index(Request $request)
    {
        try {
            $result = $this->getProductsUseCase->execute(
                search: $request->query('search', ''),
                sortBy: $request->query('sort_by', 'id'),
                sortOrder: $request->query('sort_order', 'ASC'),
                page: (int)$request->query('page', 1),
                perPage: (int)$request->query('per_page', 15)
            );

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error fetching products',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $product = $this->getProductByIdUseCase->execute($id);

            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            return response()->json($product);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error fetching product',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'sku' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0'
            ]);

            $productId = $this->createProductUseCase->execute(
                sku: $validated['sku'],
                name: $validated['name'],
                price: $validated['price'],
                stock: $validated['stock']
            );

            return response()->json([
                'message' => 'Product created successfully',
                'id' => $productId
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error creating product',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $validated = $request->validate([
                'sku' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0'
            ]);

            $affectedRows = $this->updateProductUseCase->execute(
                productId: $id,
                sku: $validated['sku'],
                name: $validated['name'],
                price: $validated['price'],
                stock: $validated['stock']
            );

            if ($affectedRows === 0) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            return response()->json([
                'message' => 'Product updated successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error updating product',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $affectedRows = $this->deleteProductUseCase->execute($id);

            if ($affectedRows === 0) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            return response()->json([
                'message' => 'Product deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error deleting product',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function checkStock(Request $request, int $id)
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1'
            ]);

            $result = $this->checkProductStockUseCase->execute(
                productId: $id,
                quantity: $validated['quantity']
            );

            if (!$result) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error checking stock',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
