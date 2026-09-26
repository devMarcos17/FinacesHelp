<?php

namespace Tests\App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

// php artisan test --filter UserControllerTest
class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    private User $user;
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
    }

    // php artisan test --filter UserControllerTest::test_user_register_validation_fails
    public function test_user_register_validation_fails(): void
    {
        $response = $this->postJson(
            '/api/auth/register',
            [
                'name' => 'Marcos',
                'email' => '@gmail.com',
                'password' => bcrypt('1234567'),
                'phone' => '21999999999',
                'cpf' => '12345678900',
                'date_of_birt' => '22/09/2006',
                'status' => 'ativo',
                'role' => 'admin',
            ]
        );
        $response->assertUnprocessable();
        $response->assertJsonStructure(['errors' => ['email']]);
    }
    // php artisan test --filter UserControllerTest::test_user_login_sucess
    public function test_user_login_sucess(): void
    {
        $response = $this->postJson('api/auth/login', [
            'email' => 'marcos@gmail.com',
            'password' => '1234567',

        ]);
        $response->assertOk();
        $response->assertJsonStructure(['access_token', 'token_type', 'expires_in', 'role']);
    }

    // php artisan test --filter UserControllerTest::test_user_delete_sucess
    public function test_user_delete_sucess(): void
    {
        $response = $this->actingAs($this->user)->deleteJson('api/auth/delete', [
            'id' => $this->user->id,
        ]);

        $response->assertOk();
        $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
    }

    // php artisan test --filter UserControllerTest::test_user_update_sucess
    public function test_user_update_sucess(): void
    {
        $response = $this->actingAs($this->user)->putJson(
            'api/auth/update',
            [
                'id' => $this->user->id,
                'name' => 'Marcos New',
            ]
        );
        $response->assertOk();
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'name' => 'Marcos New']);
    }

    // php artisan test --filter UserControllerTest::test_user_list_user_by_id_not_found
    public function test_user_list_user_by_id_not_found(): void
    {
        $response = $this->actingAs($this->user)->getJson('api/auth/listById', [
            'id' => 999999
        ]);
        $response->assertNotFound();
        $response->assertJson(['message' => 'not found']);
    }

    // php artisan test --filter UserControllerTest::test_disable_user_sucess
    public function test_disable_user_sucess(): void
    {
        $this->user->update(['status' => 'ativo']);
        $response = $this->actingAs($this->user)->postJson('api/auth/disable', [
            'id' => $this->user->id,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'status' => 'inativo']);
    }

    // php artisan test --filter UserControllerTest::test_active_user_sucess
    public function test_active_user_sucess(): void
    {
        $response = $this->actingAs($this->user)->postJson('api/auth/active', [
            'id' => $this->user->id,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'status' => 'ativo']);
    }

    // php artisan test --filter UserControllerTest::test_search_user_sucess
    public function test_search_user_sucess(): void
    {
        $response = $this->actingAs($this->user)->postJson(
            'api/auth/search',
            [
                'search' => 'marcos',
            ]
        );
        $response->assertOk();
        $response->assertJsonFragment(['email' => 'marcos@gmail.com']);
    }
    // php artisan test --filter UserControllerTest::test_login_fails_with_invalid_credentials
    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson(
            'api/auth/login',
            [
                'email' => 'marcos@gmail.com',
                'password' => 'senha_errada',
            ]
        );
        $response->assertUnauthorized();
        $response->assertJson(
            fn(AssertableJson $json) =>
            $json->missing('access_token')->etc()
        );
    }

    // php artisan test --filter UserControllerTest::test_unauthenticated_user_cannot_access_protected_routes
    public  function test_unauthenticated_user_cannot_access_protected_routes()
    {
        $response = $this->getJson('api/auth/list');
        $response->assertUnauthorized();
    }
    // php artisan test --filter UserControllerTest::test_update_fails_with_invalid_id
    public function test_update_fails_with_invalid_id(): void
    {
        $response = $this->actingAs($this->user)->putJson('api/auth/update',
        [
            'id' => 999999,
            'name' => 'New Marcos',
        ]);

        $response->assertNotFound();
    }

    //php artisan test --filter UserControllerTest::test_list_user_by_id_success
    public function test_list_user_by_id_success(): void
    {
        $response = $this->actingAs($this->user)->getJson('api/auth/listById?id=' . $this->user->id);
        $response->assertOk();
        $response->assertJsonFragment(['email' => 'marcos@gmail.com']);
    }

    //php artisan test --filter UserControllerTest::test_search_user_returns_empty_when_no_match
    public function test_search_user_returns_empty_when_no_match(): void
    {
        $response = $this->actingAs($this->user)->postJson('api/auth/search',
        [
            'search' => 'user_not_exists',
        ]);
        $response->assertOk();
        $response->assertJson(['user' => []]);
    }
}
