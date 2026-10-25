<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->string('q'));

        $products = Product::with('category')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhereHas('category', function ($catQuery) use ($q) {
                            $catQuery->where('name', 'like', "%{$q}%");
                        });
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products', 'q'));
    }

    public function create()
    {
        $categories = Category::orderByRaw("CASE WHEN name = 'Lainnya' THEN 1 ELSE 0 END, id ASC")->get();
        return view('products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        Product::create($request->validated());
        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderByRaw("CASE WHEN name = 'Lainnya' THEN 1 ELSE 0 END, id ASC")->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(StoreProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }
}