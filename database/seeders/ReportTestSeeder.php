<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportTestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create([
            'name' => 'Kasir Demo',
            'email' => 'kasir.demo@pos.test',
            'role' => 'kasir',
        ]);

        $products = Product::with('category')->get();

        if ($products->isEmpty()) {
            $this->command->warn('Tidak ada produk di database. Jalankan db:seed utama terlebih dahulu.');
            return;
        }

        // Definisi skenario transaksi per rentang tanggal untuk memudahkan testing
        $scenarios = [
            // 1. Tahun Lalu (2025)
            [
                'label' => 'Tahun Lalu (15 Mei 2025)',
                'date' => Carbon::create(2025, 5, 15, 10, 30, 0),
                'count' => 5,
            ],
            // 2. Dua Bulan Lalu
            [
                'label' => '2 Bulan Lalu (' . now()->subMonths(2)->format('F Y') . ')',
                'date' => now()->subMonths(2)->startOfMonth()->addDays(10)->setTime(14, 15),
                'count' => 8,
            ],
            // 3. Bulan Lalu
            [
                'label' => 'Bulan Lalu (' . now()->subMonth()->format('F Y') . ' - Awal Bulan)',
                'date' => now()->subMonth()->startOfMonth()->addDays(2)->setTime(11, 0),
                'count' => 10,
            ],
            [
                'label' => 'Bulan Lalu (' . now()->subMonth()->format('F Y') . ' - Akhir Bulan)',
                'date' => now()->subMonth()->endOfMonth()->subDays(1)->setTime(19, 45),
                'count' => 12,
            ],
            // 4. Bulan Ini (Kemarin & Hari ini)
            [
                'label' => 'Bulan Ini (7 Hari Lalu)',
                'date' => now()->subDays(7)->setTime(13, 20),
                'count' => 15,
            ],
            [
                'label' => 'Bulan Ini (Kemarin)',
                'date' => now()->subDay()->setTime(16, 0),
                'count' => 10,
            ],
            [
                'label' => 'Bulan Ini (Hari Ini)',
                'date' => now()->setTime(12, 30),
                'count' => 5,
            ],
        ];

        $summary = [];

        foreach ($scenarios as $scenario) {
            $scenarioTotal = 0;
            $txCount = $scenario['count'];
            $date = $scenario['date'];

            for ($i = 0; $i < $txCount; $i++) {
                // Pilih 1 - 3 produk acak
                $selectedProducts = $products->random(rand(1, min(3, $products->count())));
                $txTotal = 0;
                $detailsData = [];

                foreach ($selectedProducts as $prod) {
                    $qty = rand(1, 3);
                    $subtotal = $prod->price * $qty;
                    $txTotal += $subtotal;

                    $detailsData[] = [
                        'product_id' => $prod->id,
                        'qty' => $qty,
                        'subtotal' => $subtotal,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ];
                }

                $transactionId = DB::table('transactions')->insertGetId([
                    'user_id' => $user->id,
                    'total' => $txTotal,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                foreach ($detailsData as &$d) {
                    $d['transaction_id'] = $transactionId;
                }
                unset($d);

                DB::table('transaction_details')->insert($detailsData);
                $scenarioTotal += $txTotal;
            }

            $summary[] = [
                'Skenario' => $scenario['label'],
                'Tanggal' => $date->toDateString(),
                'Jumlah Transaksi' => $txCount,
                'Total Penjualan' => 'Rp ' . number_format($scenarioTotal),
            ];
        }

        $this->command->info('=== BERHASIL MENAMBAHKAN DATA TRANSAKSI UNTUK TESTING FILTER ===');
        $this->command->table(['Skenario', 'Tanggal Sample', 'Jumlah Transaksi', 'Total Penjualan'], $summary);
    }
}
