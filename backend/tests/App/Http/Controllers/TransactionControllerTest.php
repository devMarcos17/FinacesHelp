<?php

namespace Tests\App\Http\Controllers;

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
    public function test_create_transaction_success()
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
    public  function test_create_transaction_validation_fails()
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
    public function test_list_transaction_user_by_id_success()
    {
        $response = $this->actingAs($this->user)->getJson('api/transaction/listTransactionById?id=' . $this->transaction->id);
        $response->assertOk();
        $response->assertJsonFragment(['description' => 'Salary of month']);
    }

    // php artisan test --filter TransactionControllerTest::test_delete_transaction_user_by_id_sucess
    public function test_delete_transaction_user_by_id_sucess()
    {
        $response = $this->actingAs($this->user)->deleteJson('api/transaction/delete/?id=' . $this->transaction->id);
        $response->assertOk();
        $this->assertDatabaseMissing('transactions', ['id' => $this->transaction->id]);
    }

    //php artisan test --filter TransactionControllerTest::test_filter_date_validation_fails
    public function test_filter_date_validation_fails()
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
}