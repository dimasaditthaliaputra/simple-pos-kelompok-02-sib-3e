@extends('layouts.app')
@section('title', 'Tambah Produk')
@section('content')
<div class="max-w-2xl mx-auto mt-6">
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
        {{-- Header Card --}}
        <div class="bg-gray-50 border-b border-gray-100 px-6 py-5">
            <h1 class="text-xl font-bold text-gray-800">Tambah Produk Baru</h1>
            <p class="text-sm text-gray-500 mt-1">Silakan lengkapi detail informasi produk di bawah ini.</p>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('products.store') }}">
                @csrf
                <div class="space-y-5">
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Nama Produk</span>
                        <input type="text" name="name" value="{{ old('name') }}" 
                            class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-150 ease-in-out" 
                            placeholder="Contoh: Kopi Susu Aren">
                        @error('name')
                            <p class="text-sm text-red-500 mt-1.5 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Kategori</span>
                        <select name="category_id" 
                            class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-150 ease-in-out">
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-sm text-red-500 mt-1.5 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Input Harga --}}
                        <label class="block">
                            <span class="text-sm font-semibold text-gray-700">Harga (Rp)</span>
                            <input type="number" name="price" value="{{ old('price') }}" 
                                class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-150 ease-in-out"
                                placeholder="0">
                            @error('price')
                                <p class="text-sm text-red-500 mt-1.5 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </label>
                        <label class="block">
                            <span class="text-sm font-semibold text-gray-700">Jumlah Stok</span>
                            <input type="number" name="stock" value="{{ old('stock') }}" 
                                class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-150 ease-in-out"
                                placeholder="0">
                            @error('stock')
                                <p class="text-sm text-red-500 mt-1.5 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </label>
                    </div>
                </div>
                <div class="mt-8 flex items-center justify-end gap-3 pt-5 border-t border-gray-100">
                    <a href="{{ route('products.index') }}" 
                        class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                        class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 shadow-md transition-all">
                        Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection