@extends('layouts.app')
@section('title', 'Kasir')
@section('content')
<h1 class="text-lg font-semibold mb-4">Transaksi Kasir</h1>
<div x-data="{
    cart: [],
    addToCart(id, name, price) {
        this.cart.push({ id, name, price });
    },
    removeFromCart(id) {
        const index = this.cart.findIndex(item => item.id === id);
        if (index !== -1) {
            this.cart.splice(index, 1);
        }
    },
    subtotal() {
        return this.cart.reduce((sum, item) => sum + item.price, 0);
    }
}">
    <div class="grid grid-cols-3 gap-4">
        @foreach ($products as $product)
        <div class="border rounded-md p-3 cursor-pointer relative"
             @click="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})">
            
            <div class="flex justify-between items-start">
                <p class="font-medium">{{ $product->name }}</p>
                
                {{-- Badge Stok Menipis --}}
                @if ($product->stock < 10)
                    <span class="bg-amber-100 text-amber-700 text-xs font-semibold px-2 py-0.5 rounded">
                        Stok Menipis
                    </span>
                @endif
            </div>

            <p class="text-sm text-slate-500">Rp {{ number_format($product->price) }}</p>
            <p class="text-xs text-slate-400 mt-1">Stok: {{ $product->stock }}</p>
        </div>
        @endforeach
    </div>

    <div class="mt-4 border-t pt-3">
        <h2 class="font-semibold mb-2">Keranjang Belanja</h2>
        <template x-for="item in cart" :key="item.id">
            <div class="flex justify-between items-center py-1">
                <p x-text="item.name + ' - Rp ' + item.price"></p>
                
                <button type="button" 
                        @click="removeFromCart(item.id)" 
                        class="text-xs bg-red-100 text-red-600 hover:bg-red-200 px-2 py-1 rounded">
                    Hapus
                </button>
            </div>
        </template>

        <p class="font-semibold mt-2 border-t pt-2">
            Subtotal: Rp <span x-text="subtotal()"></span>
        </p>
    </div>
</div>
@endsection