@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-xl font-bold text-gray-800">Manajemen Kategori</h1>
    </div>

    {{-- Feedback Messages --}}
    @if (session('status'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Form Tambah Kategori --}}
    <div class="bg-white border rounded-xl p-4 sm:p-5 mb-6 shadow-sm">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Tambah Kategori Baru</h2>
        <form method="POST" action="{{ route('categories.store') }}" class="flex flex-col sm:flex-row gap-3">
            @csrf
            <div class="flex-1">
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama kategori..." required
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition-colors whitespace-nowrap">
                Tambah Kategori
            </button>
        </form>
    </div>

    {{-- Pencarian & Pengurutan --}}
    <form method="GET" action="{{ route('categories.index') }}" class="flex flex-wrap items-end gap-3 mb-4 bg-gray-50 p-3 rounded-lg border">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Cari Kategori</label>
            <input type="text" name="q" value="{{ $q }}" placeholder="Ketik nama kategori..."
                class="w-full border rounded-md px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Urutkan</label>
            <select name="sort" class="border rounded-md px-3 py-1.5 text-sm bg-white">
                <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Nama</option>
                <option value="products_count" {{ $sort === 'products_count' ? 'selected' : '' }}>Jumlah Produk</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Arah</label>
            <select name="direction" class="border rounded-md px-3 py-1.5 text-sm bg-white">
                <option value="asc" {{ $direction === 'asc' ? 'selected' : '' }}>A-Z / Terkecil</option>
                <option value="desc" {{ $direction === 'desc' ? 'selected' : '' }}>Z-A / Terbanyak</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-3 py-1.5 rounded-md text-sm">
                Filter
            </button>
            @if ($q || $sort !== 'name' || $direction !== 'asc')
                <a href="{{ route('categories.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1.5 rounded-md text-sm">
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Tabel Kategori --}}
    <div class="bg-white border rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600">
                    <th class="py-3 px-4 font-semibold">Nama Kategori</th>
                    <th class="py-3 px-4 font-semibold text-center w-36">Jumlah Produk</th>
                    <th class="py-3 px-4 font-semibold text-right w-48">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($categories as $category)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4 font-medium text-gray-900">
                            {{-- Form edit inline toggleable --}}
                            <form method="POST" action="{{ route('categories.update', $category) }}" class="flex items-center gap-2" id="form-edit-{{ $category->id }}">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $category->name }}" required
                                    class="border rounded px-2 py-1 text-sm font-medium text-gray-800 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <button type="submit" title="Simpan Perubahan" class="text-xs text-blue-600 hover:text-blue-800 font-semibold px-2 py-1 border border-blue-200 rounded hover:bg-blue-50">
                                    Simpan
                                </button>
                            </form>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-block px-2.5 py-0.5 text-xs font-medium rounded-full {{ $category->products_count > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $category->products_count }} produk
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            @if ($category->products_count == 0)
                                <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori {{ $category->name }}?')" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium px-2 py-1 border border-red-200 rounded hover:bg-red-50">
                                        Hapus
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400 italic" title="Kategori memiliki produk, tidak bisa dihapus">Terkunci</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-8 text-center text-gray-500">
                            Tidak ada kategori yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</div>
@endsection
