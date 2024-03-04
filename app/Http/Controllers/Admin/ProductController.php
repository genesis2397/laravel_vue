<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->when(request('query'), function ($query, $searchQuery) {
                $query->where('id', 'like', "%{$searchQuery}%")
                      ->orWhere('product_name', 'like', "%{$searchQuery}%")
                      ->orWhere('description', 'like', "%{$searchQuery}%");
            })
            ->latest()
            ->paginate(setting('pagination_limit'));

        return $products;
    }

    public function store()
    {
        request()->validate([
            'product_name' => 'required',
            'description' => 'nullable|string|max:255',
            'video_link' => 'nullable|url|max:255',
        ]);

        return Product::create([
            'product_name' => request('product_name'),
            'description' => request('description') ?? null,
            'video_link' => request('video_link') ?? null,
        ]);
    }

    public function update(Product $product)
    {
        request()->validate([
            'product_name' => 'required',
            'description' => 'nullable|string|max:255',
            'video_link' => 'nullable|url|max:255',
        ]);

        $product->update([
            'product_name' => request('product_name'),
            'description' => request('description') ?? null,
            'video_link' => request('video_link') ?? null,
        ]);

        return response()->json(request()->all());

    }

    public function changeRole(User $user)
    {
        $user->update([
            'role' => request('role'),
        ]);

        return response()->json(['success' => true]);
    }

    public function bulkDelete()
    {
        Product::whereIn('id', request('ids'))->delete();

        return response()->json(['message' => 'Users deleted successfully!']);
    }

    public function destory(Product $product)
    {
        $product->delete();

        return response()->noContent();
    }
}
