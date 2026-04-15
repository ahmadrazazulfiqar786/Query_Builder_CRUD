<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Throwable;

#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Bearer'
)]
class ProductController extends Controller
{
    public function __construct(private ProductRepository $productRepository)
    {
    }

    #[OA\Get(
        path: '/api/products',
        summary: 'Get all products',
        security: [['bearerAuth' => []]],
        tags: ['Products']
    )]
    #[OA\Response(response: 200, description: 'Products fetched successfully')]
    #[OA\Response(response: 500, description: 'Server error')]
    public function index(): JsonResponse
    {
        try {
            $products = $this->productRepository->all();

            return response()->json([
                'status' => true,
                'message' => 'Products fetched successfully',
                'data' => $products,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[OA\Post(
        path: '/api/products',
        summary: 'Create product',
        security: [['bearerAuth' => []]],
        tags: ['Products']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'price', 'stock'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Laptop'),
                new OA\Property(property: 'description', type: 'string', example: 'Gaming laptop'),
                new OA\Property(property: 'price', type: 'number', format: 'float', example: 150000),
                new OA\Property(property: 'stock', type: 'integer', example: 10)
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'Product created successfully')]
    #[OA\Response(response: 422, description: 'Validation error')]
    #[OA\Response(response: 500, description: 'Server error')]
    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productRepository->create($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Product created successfully',
                'data' => $product,
            ], 201);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Product creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[OA\Get(
        path: '/api/products/{id}',
        summary: 'Get single product',
        security: [['bearerAuth' => []]],
        tags: ['Products']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(response: 200, description: 'Product fetched successfully')]
    #[OA\Response(response: 404, description: 'Product not found')]
    #[OA\Response(response: 500, description: 'Server error')]
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productRepository->findById($id);

            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Product fetched successfully',
                'data' => $product,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[OA\Put(
        path: '/api/products/{id}',
        summary: 'Update product',
        security: [['bearerAuth' => []]],
        tags: ['Products']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Updated Laptop'),
                new OA\Property(property: 'description', type: 'string', example: 'Updated gaming laptop'),
                new OA\Property(property: 'price', type: 'number', format: 'float', example: 160000),
                new OA\Property(property: 'stock', type: 'integer', example: 5)
            ]
        )
    )]
    #[OA\Response(response: 200, description: 'Product updated successfully')]
    #[OA\Response(response: 404, description: 'Product not found')]
    #[OA\Response(response: 422, description: 'Validation error')]
    #[OA\Response(response: 500, description: 'Server error')]
    public function update(ProductRequest $request, int $id): JsonResponse
    {
        try {
            $existingProduct = $this->productRepository->findById($id);

            if (!$existingProduct) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            $product = $this->productRepository->update($id, $request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully',
                'data' => $product,
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Product update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    #[OA\Delete(
        path: '/api/products/{id}',
        summary: 'Delete product',
        security: [['bearerAuth' => []]],
        tags: ['Products']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(response: 200, description: 'Product deleted successfully')]
    #[OA\Response(response: 404, description: 'Product not found')]
    #[OA\Response(response: 500, description: 'Server error')]
    public function destroy(int $id): JsonResponse
    {
        try {
            $existingProduct = $this->productRepository->findById($id);

            if (!$existingProduct) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            $this->productRepository->delete($id);

            return response()->json([
                'status' => true,
                'message' => 'Product deleted successfully',
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Product deletion failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
