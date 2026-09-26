<?php

namespace Tests\App\Http\Controllers;

use App\Enums\TransactionCategory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;
    private User $user;
    private Transaction $transaction;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Marcos',
            'email' => 'marcos@gmail.com',
            'password' => bcrypt('1234567'),
            'phone' => '21999999999',
            'cpf' => '12345678900',
            'date_of_birt' => '22/09/2006',
            'status' => 'inativo',
            'role' => 'user',
        ]);
        $this->transaction = Transaction::create([
            'id_user' => $this->user->id,
            'type' => 'revenue',
            'amount' => 2500,
            'category' => 'salary',
            'description' => 'Salary of month',
        ]);
    }

    // php artisan test --filter TransactionControllerTest::test_create_transaction_success
    public function test_create_transaction_success(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/transaction/create', [
            'type' => 'revenue',
            'amount' => 2500,
            'category' => 'salary',
            'description' => 'salary of month',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('transactions', ['id_user' => $this->user->id, 'type' => 'revenue']);
    }

    // php artisan test --filter TransactionControllerTest::test_create_transaction_validation_fails
    public function test_create_transaction_validation_fails(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/transaction/create', [
            'amount' => 2500,
            'category' => 'salary',
            'description' => 'salary of month',
        ]);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['type']);
    }

    // php artisan test --filter TransactionControllerTest::test_list_transaction_user_by_id_success
    public function test_list_transaction_user_by_id_success(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('api/transaction/listTransactionById?id=' . $this->transaction->id);
        $response->assertOk();
        $response->assertJsonFragment(['description' => 'Salary of month']);
    }

    // php artisan test --filter TransactionControllerTest::test_delete_transaction_user_by_id_success
    public function test_delete_transaction_user_by_id_success(): void
    {
        $response = $this->actingAs($this->user)->deleteJson('api/transaction/delete', [
            'id' => $this->transaction->id
        ]);
        $response->assertOk();
        $this->assertDatabaseMissing('transactions', ['id' => $this->transaction->id]);
    }

    // php artisan test --filter TransactionControllerTest::test_filter_date_validation_fails
    public function test_filter_date_validation_fails(): void
    {
        $response = $this->actingAs($this->user)->postJson(
            'api/transaction/filter',
            [
                'month' => 19,
            ]
        );
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['month']);
    }

    // php artisan test --filter TransactionControllerTest::test_expenses_categories_enum_validation_fails
    public function test_expenses_categories_enum_validation_fails(): void
    {
        $response = $this->actingAs($this->user)->postJson('api/transaction/expensesCategories', [
            'category' => 'category_dont_exist',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['category']);
    }

    // php artisan test --filter TransactionControllerTest::test_expenses_categories_success
    public function test_expenses_categories_success(): void
    {
        $response = $this->actingAs($this->user)->postJson('api/transaction/expensesCategories', [
            'category' => TransactionCategory::SALARY->value
        ]);
        $response->assertOk();
        $response->assertJson([
            'transactions' => [
                [
                    'category' => TransactionCategory::SALARY->value,
                ]
            ]
        ]);
    }

    // php artisan test --filter TransactionControllerTest::test_year_transaction_validation_fails
    public function test_year_transaction_validation_fails(): void
    {
        $response = $this->actingAs($this->user)->postJson(
            'api/transaction/filter',
            [
                'month' => 5,
                'year' => 22,
            ],
        );

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['year']);
    }

    // php artisan test --filter TransactionControllerTest::test_list_transaction_by_id_not_found
    public function test_list_transaction_by_id_not_found(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('api/transaction/listTransactionById?id=' . 9999999);

        $response->assertNotFound();
        $response->assertJson(['message' => 'not found']);
    }

    // php artisan test --filter TransactionControllerTest::test_update_transaction_success
    public function test_update_transaction_success(): void
    {
        $response = $this->actingAs($this->user)->putJson('api/transaction/update', [
            'id' => $this->transaction->id,
            'amount' => 200,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('transactions', [
            'id' => $this->transaction->id, 
            'amount' => 200
        ]);
    }

    // php artisan test --filter TransactionControllerTest::test_monthly_revenue_default
    public function test_monthly_revenue_default(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('api/transaction/monthlyRevenue', [
                'month' => 5,
            ]);
        $response->assertOk();
        $this->assertDatabaseHas('transactions', ['id_user' => $this->user->id, 'amount' => $this->transaction->amount]);
    }
}