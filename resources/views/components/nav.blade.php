<nav class="bg-slate-900 text-white px-6 py-4 flex items-center gap-6 shadow-md">
    <span class="font-bold text-lg tracking-wide">Simple POS</span>
    <div class="flex items-center gap-4">
        <a href="{{ route('pos.create') }}" 
           class="px-3 py-1.5 rounded-md transition-colors {{ request()->routeIs('pos.create') ? 'bg-blue-600 text-white font-medium shadow' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
           Kasir
        </a>
        <a href="{{ route('transactions.index') }}" 
           class="px-3 py-1.5 rounded-md transition-colors {{ request()->routeIs('transactions.index') ? 'bg-blue-600 text-white font-medium shadow' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
           Transaksi
        </a>
        <a href="{{ route('products.index') }}" class="hover:underline">Produk</a>
    </div>
</nav>