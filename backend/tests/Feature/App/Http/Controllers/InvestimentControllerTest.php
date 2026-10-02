<?php

namespace Tests\Feature\App\Http\Controllers;


use App\Models\Investiment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// php artisan test --filter InvestimentControllerTest
class InvestimentControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Investiment $investiment;

    protected function setUp(): void
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
            'role' => 'admin',
        ]);

        $this->investiment = Investiment::create([
            'id_user' => $this->user->id,
            'title' => 'CDB Banco Inter',
            'amount_invested' => 1000.00,
            'current_amount' => 1250.00,
        ]);
    }

    //php artisan test --filter InvestimentControllerTest::test_create_investiment_success
    public function test_create_investiment_success(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/investiment/create', [
            'title' => 'Tesouro Selic',
            'amount_invested' => 500.00,
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('investiments', [
            'id_user' => $this->user->id,
            'title' => 'Tesouro Selic',
            'amount_invested' => 500.00,
            'current_amount' => 500.00,
        ]);
    }

    //php artisan test --filter InvestimentControllerTest::test_create_investiment_validation_fails
    public function test_create_investiment_validation_fails(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/investiment/create', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['title', 'amount_invested']);
    }

    //php artisan test --filter InvestimentControllerTest::test_update_investiment_success
    public function test_update_investiment_success(): void
    {
        $response = $this->actingAs($this->user)->putJson('/api/investiment/update', [
            'id' => $this->investiment->id,
            'title' => 'CDB 100% CDI Inter',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('investiments', [
            'id' => $this->investiment->id,
            'title' => 'CDB 100% CDI Inter',
        ]);
    }

    //php artisan test --filter InvestimentControllerTest::test_delete_investiment_success
    public function test_delete_investiment_success(): void
    {
        $response = $this->actingAs($this->user)->deleteJson('/api/investiment/delete', [
            'id' => $this->investiment->id,
        ]);

        $response->assertOk();

        $this->assertDatabaseMissing('investiments', [
            'id' => $this->investiment->id,
        ]);
    }

    //php artisan test --filter InvestimentControllerTest::test_list_investiments_user
    public function test_list_investiments_user(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/investiment/list');

        $response->assertOk();
        $response->assertJsonCount(1, 'investiments');
    }

    //php artisan test --filter InvestimentControllerTest::test_list_investiment_by_id_success
    public function test_list_investiment_by_id_success(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('api/investiment/listInvestimentId?id=' . $this->investiment->id);

        $response->assertOk();
    }

    //php artisan test --filter InvestimentControllerTest::test_profitability_calculation
    public function test_profitability_calculation(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/investiment/profitability', [
            'id' => $this->investiment->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'investiment_id' => $this->investiment->id,
            'profitability' => 25.0,
        ]);
    }

    //php artisan test --filter InvestimentControllerTest::test_deposit_investiment_success
    public function test_deposit_investiment_success(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/investiment/deposit', [
            'id' => $this->investiment->id,
            'amount' => 500.00,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('investiments', [
            'id' => $this->investiment->id,
            'amount_invested' => 1500.00,
            'current_amount' => 1750.00,
        ]);
    }

    //php artisan test --filter InvestimentControllerTest::test_with_draw_investiment_sucess
    public function test_with_draw_investiment_sucess(): void
    {
        $investiment = Investiment::create([
            'id_user' => $this->user->id,
            'title' => 'CDB Banco Inter',
            'amount_invested' => 500.00,
            'current_amount' => 1250.00
        ]);
        $response = $this->actingAs($this->user)->postJson('api/investiment/withDraw',[
            'id' => $investiment->id,
            'devolution_amount' => 20
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('investiments', ['id' => $investiment->id, 'current_amount' => 1230.00]);
        $this->assertDatabaseHas('transactions', [
        'id_user' => $this->user->id,
        'type' => 'revenue',
        'amount' => 20,
        'category' => 'other',
        'description' => 'Investiment'
    ]);
    }
}
