@extends('layouts.app')

@section('title', 'Import Produk')

@section('content')
<div class="max-w-2xl mx-auto p-4 sm:p-6">
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

        <label class="block mb-3">
            <span class="text-sm font-medium text-gray-700">Metode</span>
            <select name="driver" class="mt-1 block w-full text-sm border border-slate-300 rounded-md bg-slate-50 p-2">
                <option value="manual">Manual (str_getcsv)</option>
                <option value="excel">Laravel Excel (chunk)</option>
            </select>
        </label>

        <p class="text-xs text-slate-500 mb-4">
            Column yang dibutuhkan: <code>name</code>, <code>category_id</code>, <code>price</code>.
        </p>

        <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
            Import
        </button>
    </form>

    @if ($latest)
        <div class="bg-white border rounded-lg p-5 shadow-sm mt-6">
            <h2 class="text-sm font-semibold mb-1">Import terakhir: {{ $latest->filename }}</h2>
            <p class="text-xs text-slate-500 mb-3">
                {{ $latest->driver }} &middot; status: {{ $latest->status }}
                @if (in_array($latest->status, ['pending', 'processing']))
                    (muat ulang halaman untuk melihat hasil)
                @endif
            </p>
            <p class="text-sm mb-3">
                <span class="text-green-700">{{ $latest->success_count }} berhasil</span>,
                <span class="text-red-600">{{ $latest->failed_count }} baris gagal</span>
            </p>

            @if ($latest->errors->isNotEmpty())
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-500 border-b">
                            <th class="py-1 pr-3 w-16">Baris</th>
                            <th class="py-1">Pesan error</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($latest->errors as $error)
                            <tr class="border-b last:border-0">
                                <td class="py-1 pr-3">{{ $error->row_number }}</td>
                                <td class="py-1 text-red-600">{{ $error->message }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif
</div>
@endsection
