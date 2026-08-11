<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = Category::create([
            'user_id' => $this->user->id,
            'name' => 'Food',
            'type' => 'expense'
        ]);
    }

    public function test_user_can_create_transaction()
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/transactions', [
            'category_id' => $this->category->id,
            'type' => 'expense',
            'amount' => 50000,
            'transaction_date' => '2026-08-11',
            'merchant_name' => 'Warung',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.amount', 50000);
                 
        $this->assertDatabaseHas('transactions', ['merchant_name' => 'Warung']);
    }

    public function test_user_can_get_transactions()
    {
        $this->actingAs($this->user)->postJson('/api/v1/transactions', [
            'category_id' => $this->category->id,
            'type' => 'expense',
            'amount' => 50000,
            'transaction_date' => '2026-08-11',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/v1/transactions');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }
}
