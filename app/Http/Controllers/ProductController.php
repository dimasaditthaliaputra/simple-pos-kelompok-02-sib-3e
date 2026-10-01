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
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        Product::create($request->validated());
        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        return "Form edit produk #{$id} (belum dibuat)";
    }

    public function update(string $id)
    {
        return "Produk #{$id} diperbarui (belum ada logika penyimpanan)";
    }
}