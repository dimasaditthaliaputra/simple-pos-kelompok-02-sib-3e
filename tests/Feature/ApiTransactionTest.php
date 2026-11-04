<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTransactionTest extends TestCase
{
    use RefreshDatabase;

    private function payload(): array
    {
        $category = Category::forceCreate([
            'name' => 'Minuman',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Kopi',
            'price' => 15000,
            'stock' => 10,
        ]);

        return [
            'items' => [
                [
                    'product_id' => $product->id,
                    'qty' => 2,
                ],
            ],
        ];
    }

    public function test_guest_cannot_create_transaction(): void
    {
        $this->postJson('/api/v1/transactions', $this->payload())
            ->assertUnauthorized();
    }

    public function test_token_without_ability_is_forbidden(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['products:read']
        );

        $this->postJson(
            '/api/v1/transactions',
            $this->payload(),
            ['Idempotency-Key' => 'k1']
        )->assertForbidden();
    }

    public function test_invalid_items_are_rejected(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['transactions:create']
        );

        $this->postJson(
            '/api/v1/transactions',
            [
                'items' => [
                    ['product_id' => 99999, 'qty' => 1],
                ],
            ],
            ['Idempotency-Key' => 'k2']
        )
            ->assertUnprocessable()
            ->assertJsonValidationErrors('items.0.product_id');
    }

    public function test_transaction_is_created_once_even_when_repeated(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['transactions:create']
        );

        $payload = $this->payload();
        $headers = ['Idempotency-Key' => 'k3'];

        $first = $this->postJson(
            '/api/v1/transactions',
            $payload,
            $headers
        )
            ->assertCreated()
            ->assertJsonPath('data.total', 30000);

        $this->postJson(
            '/api/v1/transactions',
            $payload,
            $headers
        )
            ->assertCreated()
            ->assertHeader('Idempotent-Replayed', 'true')
            ->assertJsonPath('data.id', $first->json('data.id'));

        $this->assertDatabaseCount('transactions', 1);
    }

    public function test_cashier_cannot_view_another_cashiers_transaction(): void
    {
        $owner = User::factory()->create();

        $transaction = Transaction::create([
            'user_id' => $owner->id,
            'total' => 1000,
        ]);

        Sanctum::actingAs(
            User::factory()->create(),
            ['transactions:read']
        );

        $this->getJson(
            "/api/v1/transactions/{$transaction->id}"
        )->assertForbidden();

        Sanctum::actingAs(
            $owner,
            ['transactions:read']
        );

        $this->getJson(
            "/api/v1/transactions/{$transaction->id}"
        )
            ->assertOk()
            ->assertJsonPath('data.id', $transaction->id);
    }

    public function test_expired_token_cannot_access_api(): void
    {
        $user = User::factory()->create();

        // Pastikan user dapat diautentikasi menggunakan Sanctum.
        Sanctum::actingAs($user, ['transactions:create']);

        $this->assertAuthenticatedAs($user, 'sanctum');

        // Hapus autentikasi sementara agar request berikutnya
        // benar-benar memeriksa Bearer token.
        Auth::forgetGuards();

        // Atur waktu awal pengujian.
        $issuedAt = Carbon::parse('2026-11-04 10:00:00');
        Carbon::setTestNow($issuedAt);

        try {
            // Buat token yang hanya berlaku selama satu menit.
            $token = $user->createToken(
                'expired-test',
                ['transactions:create'],
                $issuedAt->copy()->addMinute()
            );

            // Majukan waktu hingga token kedaluwarsa.
            Carbon::setTestNow($issuedAt->copy()->addMinutes(2));

            // Pastikan token sudah melewati waktu kedaluwarsa.
            $this->assertTrue(
                $token->accessToken->expires_at->isPast()
            );

            // Token kedaluwarsa harus ditolak oleh API.
            $this->postJson(
                '/api/v1/transactions',
                ['items' => []],
                ['Authorization' => 'Bearer '.$token->plainTextToken]
            )->assertUnauthorized();
        } finally {
            // Kembalikan waktu pengujian seperti semula.
            Carbon::setTestNow();
            Auth::forgetGuards();
        }
    }
}
