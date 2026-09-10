@extends('layouts.app')
@section('title', 'Kasir')

@section('content')
<div class="p-6">
    <h1 class="text-xl font-bold mb-6 text-slate-800">Transaksi Kasir</h1>

    <div x-data="{
        cart: [],
        selectedId: null,
        addToCart(id, name, price) {
            this.selectedId = id; // Menyimpan ID produk yang diklik untuk efek highlight
            
            let item = this.cart.find(i => i.id === id);
            if (item) {
                item.qty++;
            } else {
                this.cart.push({ id, name, price, qty: 1 });
            }
        },
        subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        }
    }">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($products as $product)
            <div @click="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})"
                 :class="selectedId === {{ $product->id }} ? 'ring-2 ring-blue-500 bg-blue-50/50 shadow-md' : 'bg-white'"
                 class="border border-slate-200 rounded-lg p-4 cursor-pointer transition-all hover:shadow-lg flex flex-col justify-between">
                
                <div>
                    <div class="flex justify-between items-start gap-2">
                        <p class="font-semibold text-slate-800">{{ $product->name }}</p>
                        @if($product->stock < 10)
                            <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium">Stok Menipis</span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-500 mt-1">Rp {{ number_format($product->price) }}</p>
                </div>
                
                <p class="text-xs text-slate-400 mt-4">Stok: {{ $product->stock }}</p>
            </div>
            @endforeach
        </div>

        <!-- Area Keranjang Belanja -->
        <div class="mt-8 bg-white border border-slate-200 rounded-lg p-5 shadow-sm">
            <h2 class="font-bold text-lg mb-3 text-slate-800">Keranjang Belanja</h2>
            
            <template x-if="cart.length === 0">
                <p class="text-sm text-slate-400 italic">Keranjang masih kosong. Klik produk di atas untuk menambahkan.</p>
            </template>

            <div class="space-y-2">
                <template x-for="item in cart" :key="item.id">
                    <div class="flex justify-between items-center text-sm border-b pb-2">
                        <span x-text="item.name + ' (x' + item.qty + ')'"></span>
                        <span class="font-medium" x-text="'Rp ' + (item.price * item.qty).toLocaleString()"></span>
                    </div>
                </template>
            </div>

            <p class="font-bold text-base mt-4 pt-2 border-t flex justify-between">
                <span>Subtotal:</span>
                <span x-text="'Rp ' + subtotal().toLocaleString()"></span>
            </p>
        </div>
    </div>
</div>
@endsection