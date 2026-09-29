<?php

namespace Tests\App\Http\Controllers;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
    public function test_create_goal_sucess()
    {
        $response = $this->actingAs($this->user)->postJson('api/goals/create',[
        'id_user' => $this->user->id,
        'title'=> 'Comprar um Carro Novo',
        'description'=> 'Economizar para dar entrada no veículo SUV.',
        'target_year'=> 2027,
        'target_date'=> '2027-12-31',
        'target_amount'=> 45000.50,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('goals', ['id_user' => $this->user->id, 'title' => 'Comprar um Carro Novo']);
    }

    // php artisan test --filter GoalControllerTest::test_create_goal_validation_fails
    public function test_create_goal_validation_fails()
    {
        $response = $this->actingAs($this->user)->postJson('api/goals/create',[
        'id_user' => $this->user->id,
        'description'=> 'Economizar para dar entrada no veículo SUV.',
        'target_year'=> 2027,
        'target_date'=> '2027-12-31',
        'target_amount'=> 45000.50,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['title']);
    }

    // php artisan test --filter GoalControllerTest::test_user_can_create_a_goal_with_valid_data
    public function test_user_can_create_a_goal_with_valid_data()
    {
         $response = $this->actingAs($this->user)->postJson('api/goals/create',[
        'id_user' => $this->user->id,
        'title' => 'Comprar um carro novo',
        'description'=> 'Economizar para dar entrada no veículo SUV.',
        'target_year'=> 2027,
        'target_date'=> '2027-12-31',
        'target_amount'=> 45000.50,
        ]);
        $response->assertCreated();
        $this->assertDatabaseHas('goals', ['id_user' => $this->user->id, 'target_amount' => 0]);
    }
}