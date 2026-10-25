@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div class="max-w-5xl mx-auto p-4 sm:p-6">
    <h1 class="text-xl font-bold text-gray-800 mb-4">Daftar Produk</h1>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-3 rounded-lg mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <a href="{{ route('products.create') }}" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
            + Tambah Produk
        </a>

        {{-- Form Pencarian Produk --}}
        <form method="GET" action="{{ route('products.index') }}" class="flex items-center gap-2">
            <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari nama produk atau kategori..."
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 w-64 sm:w-72">
            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Cari
            </button>
            @if (!empty($q))
                <a href="{{ route('products.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors" title="Reset Pencarian">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="bg-white border rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600">
                    <th class="py-3 px-4 font-semibold">Nama</th>
                    <th class="py-3 px-4 font-semibold">Kategori</th>
                    <th class="py-3 px-4 font-semibold">Harga</th>
                    <th class="py-3 px-4 font-semibold">Stok</th>
                    <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($products as $product)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4 font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $product->category?->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-gray-900">Rp {{ number_format($product->price) }}</td>
                        <td class="py-3 px-4 text-gray-900">{{ $product->stock }}</td>
                        <td class="py-3 px-4 text-right">
                            <a href="{{ route('products.edit', $product) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-xs border border-blue-200 px-2.5 py-1 rounded hover:bg-blue-50">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-500">
                            Tidak ada produk yang ditemukan{{ !empty($q) ? ' untuk pencarian "' . $q . '"' : '' }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection