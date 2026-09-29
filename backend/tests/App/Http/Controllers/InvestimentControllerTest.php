<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Investiment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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

    public function test_create_investiment_validation_fails(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/investiment/create', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['title', 'amount_invested']);
    }

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

    public function test_list_investiments_user(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/investiment/list');

        $response->assertOk();
        $response->assertJsonCount(1, 'investiments');
    }

    public function test_list_investiment_by_id_success(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('api/investiment/listInvestimentId?id=' . $this->investiment->id);

        $response->assertOk();
    }

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
}
