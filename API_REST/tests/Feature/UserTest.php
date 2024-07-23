<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\ClientRepository as PassportClientRepository;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserTest extends TestCase
{
   use RefreshDatabase;
   protected $admin;
   protected $anotherUser;
   protected $adminToken;

   protected function setUp(): void
    {
        parent::setUp();

        // Configurar el cliente de acceso personal para Passport
        $clientRepository = new PassportClientRepository();
        $clientRepository->createPersonalAccessClient(
            null, 'Test Personal Access Client', 'http://localhost'
        );

        //Creando usuario admin para los tests
        $this->admin = User::create([
            'nickname' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123!'),
        ]);
        $this->admin->rol = 'admin';
        $this->admin->save();
        //Para dar token
        $this->adminToken = $this->admin->createToken('AdminToken')->accessToken;
       
        //Usuario con rol de jugador
        $this->anotherUser = User::factory()->create([
            'nickname' => 'Another User',
            'email' => 'anotheruser@example.com',
            'password' => bcrypt('anotheruserpassword'),
        ]);
    }
    #[\PHPUnit\Framework\Attributes\Test] 
    public function user_can_be_registred(){

        $this->withoutExceptionHandling();

        $response = $this->post('/api/players',[
            'nickname' => 'ProbandoUser',
            'email' => 'mail@depueba.cat',
            'password' => 'Aa123456!'
        ]);

        $response->assertStatus(201);

        $this->assertCount(3, User::all());

        $user = User::orderBy('nickname', 'desc')->first();

        $this->assertEquals($user->nickname, 'ProbandoUser');
        $this->assertEquals($user->email, 'mail@depueba.cat');
    }

    public function admin_can_login_with_correct_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->admin->email,
            'password' => 'Aa123456',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'token']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_cannot_login_with_incorrect_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->admin->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'error' => 'Credenciales incorrectas',
                     'success' => false,
                 ]);
    }

    #[\PHPUnit\Framework\Attributes\Test] 
    public function user_can_login_ok(){
       
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt($password = 'password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'token',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test] 
    public function user_cant_login_with_errors(){
       
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt($password = 'password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'error' => 'Credenciales incorrectas',
                     'success' => false,
                 ]);
    }
    #[\PHPUnit\Framework\Attributes\Test] 
    public function users_can_be_retrieved(){

        $this->withoutExceptionHandling();

        User::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->adminToken",
        ])->getJson('/api/players');

        $response->assertOk()->assertJsonCount(5);

    }

    public function users_cant_be_retrieved_unauthenticated(){

        $this->withoutExceptionHandling();

        User::factory()->count(3)->create();

        $response = $this->getJson('/api/players');

        $response->assertStatus(403);

    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_update_own_nickname()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->adminToken",
        ])->putJson('/api/players/' . $this->admin->id, [
            'nickname' => 'AdminNew',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Nickname modificado, ¡Buen cambio!.',
                 ]);

        $this->admin->refresh();
        $this->assertEquals('AdminNew', $this->admin->nickname);
    }
    #[\PHPUnit\Framework\Attributes\Test]
    public function user_cannot_update_another_users_nickname()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->adminToken",
        ])->putJson('/api/players/' . $this->anotherUser->id, [
            'nickname' => 'AnotherNickname',
        ]);

        $response->assertStatus(403)
                 ->assertJson([
                     'message' => 'No puedes modificar un nickname que no sea el tuyo, que te conozco.',
                 ]);

        // Verificar que el nickname no ha cambiado
        $this->anotherUser->refresh();
        $this->assertNotEquals('AnotherNickname', $this->anotherUser->nickname);
    }
    #[\PHPUnit\Framework\Attributes\Test]
    public function update_nickname_with_empty_value()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->adminToken",
        ])->putJson('/api/players/' . $this->admin->id, [
            'nickname' => '',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Nickname modificado, ¡Buen cambio!.',
                 ]);

        // Verificar que el nickname se ha actualizado a 'Anònim'
        $this->admin->refresh();
        $this->assertEquals('Anònim', $this->admin->nickname);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_get_ranking(){

        $this->withoutExceptionHandling();
        User::factory()->count(3)->create(); 


        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->adminToken",
        ])-> getJson('api/players/ranking');

        $response->assertStatus(200);
    }

    /*#[\PHPUnit\Framework\Attributes\Test]
    public function admin_message_ranking_no_users(){
        $this->withoutExceptionHandling();
      

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->adminToken",
        ])-> getJson('api/players/ranking');


        $response->assertStatus(200);
        $response->assertJson(['message' => 'No hay jugadores registrados',]);

    }
    --> Este test no funciona porque necesita usuario autenticado, por lo cual no habra DB vacía.
    */

/*
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_no_players_registered_message_when_no_users_exist_winner()
    {
       
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->adminToken",
        ])->getJson('api/players/ranking/winner');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'No hay jugadores registrados']);
    }
    --> Este test no funciona porque necesita usuario autenticado, por lo cual no habra DB vacía.
    */
    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_returns_the_winner_when_users_exist()
    {
      
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->adminToken",
        ])-> getJson('api/players/ranking/winner');

        $response->assertStatus(200);
 
    }
    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_admin_get_loser_with_users()
    {
     
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->adminToken",
        ])-> getJson('api/players/ranking/winner');

        $response->assertStatus(200);
       
    }
}
