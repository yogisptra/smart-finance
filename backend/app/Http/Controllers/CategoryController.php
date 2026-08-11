<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('user_id', $request->user()->id)
            ->orWhere('is_default', true)
            ->paginate($request->per_page ?? 20);
            
        return response()->json([
            'success' => true,
            'message' => 'Categories retrieved successfully',
            'data' => $categories->items(),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
                'last_page' => $categories->lastPage(),
            ]
        ]);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $request->user()->categories()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    public function show(Request $request, Category $category)
    {
        if ($category->user_id && $category->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category retrieved successfully',
            'data' => $category
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        if ($category->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or default category'], 403);
        }

        $category->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    public function destroy(Request $request, Category $category)
    {
        if ($category->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or default category'], 403);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
            'data' => null
        ], 204);
    }
}
