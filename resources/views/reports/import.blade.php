@extends('layouts.app')

@section('title', 'Import Produk')

@section('content')
<div class="max-w-md mx-auto p-4 sm:p-6">
    <h1 class="text-lg font-semibold mb-4">Import Produk dari CSV</h1>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-3 rounded-md mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data" class="bg-white border rounded-lg p-5 shadow-sm">
        @csrf
        
        <label class="block mb-3">
            <span class="text-sm font-medium text-gray-700">File CSV</span>
            <input type="file" name="csv" accept=".csv" required
                class="mt-1 block w-full text-sm text-slate-500 border border-slate-300 rounded-md cursor-pointer bg-slate-50 focus:outline-none file:mr-3 file:py-2 file:px-3 file:rounded-l-md file:border-0 file:text-sm file:font-medium file:bg-slate-900 file:text-white hover:file:bg-slate-800 transition">
            @error('csv')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </label>

        <p class="text-xs text-slate-500 mb-4">
            Column yang dibutuhkan: <code>name</code>, <code>category_id</code>, <code>price</code>.
        </p>

        <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
            Import
        </button>
    </form>
</div>
@endsection
