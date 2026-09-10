@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
    <h1 class="text-lg font-semibold mb-4">Transaksi Kasir</h1>

    <div
        x-data="{
            cart: [],
            selectedId: null,

            addToCart(id, name, price) {
                this.selectedId = id;

                let item = this.cart.find(i => i.id === id);

                if (item) {
                    item.qty++;
                } else {
                    this.cart.push({
                        id,
                        name,
                        price,
                        qty: 1
                    });
                }
            },

            removeFromCart(id) {
                this.cart = this.cart.filter(item => item.id !== id);
            },

            subtotal() {
                return this.cart.reduce(
                    (sum, item) => sum + (item.price * item.qty),
                    0
                );
            }
        }"
    >
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($products as $product)
                <div
                    @click="addToCart({{ $product->id }}, @js($product->name), {{ $product->price }})"
                    :class="selectedId === {{ $product->id }}
                        ? 'ring-2 ring-blue-500 bg-blue-50/50 shadow-md'
                        : 'bg-white'"
                    class="border border-slate-200 rounded-lg p-4 cursor-pointer transition-all hover:shadow-lg"
                >
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-semibold text-slate-800">
                            {{ $product->name }}
                        </p>

                        @if ($product->stock < 10)
                            <span class="bg-amber-100 text-amber-700 text-xs font-semibold px-2 py-0.5 rounded">
                                Stok Menipis
                            </span>
                        @endif
                    </div>

                    <p class="text-sm text-slate-500 mt-1">
                        Rp {{ number_format($product->price) }}
                    </p>

                    <p class="text-xs text-slate-400 mt-4">
                        Stok: {{ $product->stock }}
                    </p>
                </div>
            @endforeach
        </div>

        <div class="mt-4 border-t pt-3">
            <h2 class="font-semibold mb-2">Keranjang Belanja</h2>

            <template x-if="cart.length === 0">
                <p class="text-sm text-slate-400 italic">
                    Keranjang masih kosong. Klik produk di atas untuk menambahkan.
                </p>
            </template>

            <div class="space-y-2">
                <template x-for="item in cart" :key="item.id">
                    <div class="flex items-center justify-between mb-2">
                        <p x-text="item.name + ' (x' + item.qty + ')'"></p>

                        <div class="flex items-center gap-3">
                            <span
                                class="font-medium"
                                x-text="'Rp ' + (item.price * item.qty).toLocaleString('id-ID')"
                            ></span>

                            <button
                                type="button"
                                @click="removeFromCart(item.id)"
                                class="text-red-600 hover:text-red-800"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <p class="font-semibold mt-2 border-t pt-2 flex justify-between">
                <span>Subtotal:</span>
                <span x-text="'Rp ' + subtotal().toLocaleString('id-ID')"></span>
            </p>
        </div>
    </div>
@endsection