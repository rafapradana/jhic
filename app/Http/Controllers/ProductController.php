<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::query();
            
            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                });
            }
            
            // Filter by price range
            if ($request->has('min_price') && is_numeric($request->min_price)) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->has('max_price') && is_numeric($request->max_price)) {
                $query->where('price', '<=', $request->max_price);
            }
            
            // Filter by stock range
            if ($request->has('min_stock') && is_numeric($request->min_stock)) {
                $query->where('stock', '>=', $request->min_stock);
            }
            if ($request->has('max_stock') && is_numeric($request->max_stock)) {
                $query->where('stock', '<=', $request->max_stock);
            }
            
            // Filter by category
            if ($request->has('category') && !empty($request->category)) {
                $query->where('category', $request->category);
            }
            
            // Filter by status (is_active)
            if ($request->has('is_active') && $request->is_active !== '') {
                $query->where('is_active', $request->is_active === '1' || $request->is_active === 'true');
            }
            
            // Legacy status filter support
            if ($request->has('status') && in_array($request->status, ['active', 'inactive'])) {
                $query->where('is_active', $request->status === 'active');
            }
            
            // Sorting functionality
            $sortBy = $request->get('sort_by', 'updated_at_desc'); // Default to last updated
            $allowedSorts = [
                'updated_at_desc' => ['updated_at', 'desc'],
                'updated_at_asc' => ['updated_at', 'asc'],
                'created_at_desc' => ['created_at', 'desc'],
                'created_at_asc' => ['created_at', 'asc'],
                'name_asc' => ['name', 'asc'],
                'name_desc' => ['name', 'desc'],
                'price_asc' => ['price', 'asc'],
                'price_desc' => ['price', 'desc'],
                'stock_asc' => ['stock', 'asc'],
                'stock_desc' => ['stock', 'desc']
            ];
            
            if (array_key_exists($sortBy, $allowedSorts)) {
                [$column, $direction] = $allowedSorts[$sortBy];
                $query->orderBy($column, $direction);
            } else {
                // Fallback to default sorting
                $query->orderBy('updated_at', 'desc');
            }
            
            // Get pagination parameters
            $perPage = $request->get('per_page', 10); // Default 10 items per page
            $perPage = min(max($perPage, 1), 100); // Limit between 1-100
            
            // Get paginated results
            $products = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                    'has_more_pages' => $products->hasMorePages(),
                    'prev_page_url' => $products->previousPageUrl(),
                    'next_page_url' => $products->nextPageUrl()
                ],
                'search' => [
                    'term' => $request->get('search', ''),
                    'status_filter' => $request->get('status', 'all')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category' => 'required|in:Electronics,Fashion,Home & Garden,Sports & Outdoors,Books,Health & Beauty,Automotive,Food & Beverages,Toys & Games,Office Supplies',
                'status' => 'required|in:active,inactive'
            ]);

            // Convert status to is_active boolean
            $validated['is_active'] = $validated['status'] === 'active';
            unset($validated['status']);

            $product = Product::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Product retrieved successfully',
                'data' => $product
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
                'stock' => 'sometimes|required|integer|min:0',
                'category' => 'sometimes|required|in:Electronics,Fashion,Home & Garden,Sports & Outdoors,Books,Health & Beauty,Automotive,Food & Beverages,Toys & Games,Office Supplies',
                'status' => 'sometimes|required|in:active,inactive'
            ]);

            // Convert status to is_active boolean if present
            if (isset($validated['status'])) {
                $validated['is_active'] = $validated['status'] === 'active';
                unset($validated['status']);
            }

            $product->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product->fresh()
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $productName = $product->name;
            
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => "Product '{$productName}' deleted successfully"
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unique categories from products.
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = Product::whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->pluck('category')
                ->sort()
                ->values();

            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
