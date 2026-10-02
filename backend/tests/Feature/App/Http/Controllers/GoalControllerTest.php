<?php

namespace Tests\Feature\App\Http\Controllers;


use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// php artisan test --filter GoalControllerTest
class GoalControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Goal $goal;
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
            'role' => 'admin',
        ]);
        $this->goal = Goal::create([
            'id_user'      => $this->user->id,
            'title'         => 'Comprar um Carro Novo',
            'description'   => 'Economizar para dar entrada no veículo SUV.',
            'target_year'   => 2027,
            'target_date'   => '2027-12-31',
            'image_path'    => 'car.jpg',
            'target_amount' => 45000.50,
        ]);
    }

    // php artisan test --filter GoalControllerTest::test_create_goal_sucess
    public function test_create_goal_sucess(): void
    {
        $response = $this->actingAs($this->user)->postJson('api/goals/create', [
            'id_user' => $this->user->id,
            'title' => 'Comprar um Carro Novo',
            'description' => 'Economizar para dar entrada no veículo SUV.',
            'target_year' => 2027,
            'target_date' => '2027-12-31',
            'target_amount' => 45000.50,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('goals', ['id_user' => $this->user->id, 'title' => 'Comprar um Carro Novo']);
    }

    // php artisan test --filter GoalControllerTest::test_create_goal_validation_fails
    public function test_create_goal_validation_fails(): void
    {
        $response = $this->actingAs($this->user)->postJson('api/goals/create', [
            'id_user' => $this->user->id,
            'description' => 'Economizar para dar entrada no veículo SUV.',
            'target_year' => 2027,
            'target_date' => '2027-12-31',
            'target_amount' => 45000.50,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['title']);
    }

    // php artisan test --filter GoalControllerTest::test_user_can_create_a_goal_with_valid_data
    public function test_user_can_create_a_goal_with_valid_data(): void
    {
        $response = $this->actingAs($this->user)->postJson('api/goals/create', [
            'id_user' => $this->user->id,
            'title' => 'Minha Meta de Teste',
            'target_year' => 2026,
            'current_amount' => 0,
            'target_amount' => 200.00
        ]);
        $response->assertCreated();
        $this->assertDatabaseHas('goals', ['id_user' => $this->user->id, 'current_amount' => 0]);
    }

    // php artisan test --filter GoalControllerTest::test_deposit_goal_sucess
    public function test_deposit_goal_sucess(): void
    {
        $goal = Goal::create([
            'id_user' => $this->user->id,
            'title' => 'Minha Meta de Teste',
            'target_year' => 2026,
            'current_amount' => 0,
            'target_amount' => 200.00,
            'status' => 'in_progress',
        ]);
        $response = $this->actingAs($this->user)->postJson('api/goals/deposit', [
            'id' => $this->goal->id,
            'current_amount' => 2000
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('goals', ['id' => $this->goal->id, 'current_amount' => 2000]);
    }

    // php artisan test --filter GoalControllerTest::test_deposit_goal_fails
    public function test_deposit_goal_fails()
    {
        $response = $this->actingAs($this->user)->postJson('api/goals/deposit', [
            'id' => $this->goal->id,
            'current_amount' => ''
        ]);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['current_amount']);
    }
    // php artisan test --filter GoalControllerTest::test_with_draw_devolution
    public function test_with_draw_devolution(): void
    {
        $goal = Goal::create([
            'id_user' => $this->user->id,
            'title' => 'Minha Meta de Teste',
            'target_year' => 2026,
            'current_amount' => 100.00,
            'target_amount' => 200.00,
            'status' => 'in_progress',
        ]);
        $response = $this->actingAs($this->user)->postJson('api/goals/withDraw', [
            'id' => $goal->id,
            'devolution_amount' => 70.00
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('goals', ['id' => $goal->id, 'current_amount' => 30.00]);
    }

    // php artisan test --filter GoalControllerTest::test_with_draw_devolution_fails
    public function test_with_draw_devolution_fails(): void
    {
        $response = $this->actingAs($this->user)->postJson('api/goals/withDraw', [
            'id' => $this->goal->id,
            'devolution_amount' => '',
        ]);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('devolution_amount');
    }

    // php artisan test --filter GoalControllerTest::test_with_draw_balance_insufficient
    public function test_with_draw_balance_insufficient(): void
    {
        $goal = Goal::create([
            'id_user' => $this->user->id,
            'title' => 'Minha Meta de Teste',
            'target_year' => 2026,
            'current_amount' => 100.00,
            'target_amount' => 200.00,
            'status' => 'in_progress',
        ]);
        $response = $this->actingAs($this->user)->postJson('api/goals/withDraw', [
            'id' => $goal->id,
            'devolution_amount' => 300.00
        ]);
        $response->assertBadRequest();
        $response->assertJson(['message' => 'insufficient balance']);
    }
    // php artisan test --filter GoalControllerTest::test_with_draw_update_goal
    public function test_with_draw_update_goal(): void
    {
        $goal = Goal::create([
            'id_user' => $this->user->id,
            'title' => 'Minha Meta de Teste',
            'target_year' => 2026,
            'current_amount' => 100.00,
            'target_amount' => 200.00,
            'status' => 'completed',
        ]);
        $response = $this->actingAs($this->user)->postJson('api/goals/withDraw', [
            'id' => $goal->id,
            'devolution_amount' => 70.00
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('goals', ['id' => $goal->id, 'status' => 'in_progress']);
    }

    // php artisan test --filter GoalControllerTest::test_devolution_other_users
    public function test_devolution_other_users(): void
    {
        $user = User::create([
            'name' => 'marcos other',
            'email' => 'marcosother@gmail.com',
            'password' => bcrypt('1234567'),
            'phone' => '21999999999',
            'cpf' => '12345678900',
            'date_of_birt' => '22/09/2006',
            'status' => 'inativo',
            'role' => 'admin',
        ]);
        $goal = Goal::create([
            'id_user' => $this->user->id,
            'title' => 'Minha Meta de Teste',
            'target_year' => 2026,
            'current_amount' => 100.00,
            'target_amount' => 200.00,
            'status' => 'completed',
        ]);
        $response = $this->actingAs($user)->postJson('api/goals/withDraw', [
            'id' => $goal->id,
            'devolution_amount' => 70.00
        ]);
        $response->assertNotFound();
    }

    // php artisan test --filter GoalControllerTest::test_list_goal_completed
    public function test_list_goal_completed(): void
    {
        $this->goal->update(['status' => 'completed']);

        $response = $this->actingAs($this->user)->getJson('api/goals/completed', [
            'id' => $this->goal->id
        ]);
        $response->assertOk();
        $this->assertDatabaseHas('goals', ['id' => $this->goal->id, 'status' => 'completed']);
    }

    // php artisan test --filter GoalControllerTest::test_list_goal_cancelled
    public function test_list_goal_cancelled(): void
    {
        $this->goal->update(['status' => 'cancelled']);

        $response = $this->actingAs($this->user)->getJson('api/goals/cancelled?id=' .
            $this->goal->id);
        $response->assertOk();
        $this->assertDatabaseHas('goals', ['id' => $this->goal->id, 'status' => 'cancelled']);
    }
}
