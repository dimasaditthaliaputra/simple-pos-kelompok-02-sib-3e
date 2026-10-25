<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_pdf_with_top_products_table(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $category = Category::create(['name' => 'Minuman']);

        $productA = Product::create([
            'category_id' => $category->id,
            'name' => 'Kopi Latte',
            'price' => 20000,
            'stock' => 50,
        ]);

        $productB = Product::create([
            'category_id' => $category->id,
            'name' => 'Teh Manis',
            'price' => 5000,
            'stock' => 100,
        ]);

        $transaction = Transaction::create([
            'user_id' => $admin->id,
            'total' => 50000,
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $productA->id,
            'qty' => 2,
            'subtotal' => 40000,
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $productB->id,
            'qty' => 2,
            'subtotal' => 10000,
        ]);

        $response = $this->actingAs($admin)->get(route('reports.export.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_pdf_view_renders_top_products(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $category = Category::create(['name' => 'Makanan']);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Roti Bakar',
            'price' => 15000,
            'stock' => 20,
        ]);

        $transaction = Transaction::create([
            'user_id' => $admin->id,
            'total' => 45000,
        ]);

        $detail = TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'qty' => 3,
            'subtotal' => 45000,
        ]);

        $detail->setRelation('product', $product);
        $detail->total_qty = 3;
        $detail->total_subtotal = 45000;

        $view = $this->view('reports.pdf', [
            'from' => now()->startOfMonth()->toDateString(),
            'to' => now()->toDateString(),
            'totalPenjualan' => 45000,
            'jumlahTransaksi' => 1,
            'topProducts' => collect([$detail]),
            'transactions' => collect([$transaction]),
        ]);

        $view->assertSee('Produk Terlaris');
        $view->assertSee('Roti Bakar');
        $view->assertSee('Makanan');
        $view->assertSee('Terjual');
        $view->assertSee('Total Penjualan');
    }
}
