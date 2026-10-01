<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    public function test_can_view_edit_product_page(): void
    {
        $category = new Category();
        $category->name = 'Test Category';
        $category->save();

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sample Coffee',
            'price' => 15000,
            'stock' => 10,
        ]);

        $response = $this->get(route('products.edit', $product));

        $response->assertStatus(200);
        $response->assertViewIs('products.edit');
        $response->assertViewHas('product');
        $response->assertViewHas('categories');
        $response->assertSee('Edit Produk');
        $response->assertSee($product->name);
    }

    public function test_cannot_update_product_with_invalid_data(): void
    {
        $category = new Category();
        $category->name = 'Test Category 2';
        $category->save();

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sample Coffee 2',
            'price' => 15000,
            'stock' => 10,
        ]);

        $response = $this->from(route('products.edit', $product))
            ->put(route('products.update', $product), [
                'name' => '',
                'category_id' => 999999,
                'price' => -100,
                'stock' => -5,
            ]);

        $response->assertRedirect(route('products.edit', $product));
        $response->assertSessionHasErrors(['name', 'category_id', 'price', 'stock']);
    }

    public function test_can_update_product_successfully(): void
    {
        $category = new Category();
        $category->name = 'Minuman';
        $category->save();

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Original Name',
            'price' => 20000,
            'stock' => 15,
        ]);

        $response = $this->put(route('products.update', $product), [
            'name' => 'Updated Name',
            'category_id' => $category->id,
            'price' => 25000,
            'stock' => 30,
        ]);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success', 'Produk berhasil diperbarui.');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'category_id' => $category->id,
            'price' => 25000,
            'stock' => 30,
        ]);
    }
}
