<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game;

class GameTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $otherUser;
    protected $game;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $game = Game::create([
            'user_id' => $this->user->id,
            'dice1' => 3,
            'dice2' => 4,
            'winner' => true,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function show_own_games_running()
    {
        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->user, 'api')->getJson('/api/players/' . $this->user->id . '/games');

        $response->assertStatus(200)
            ->assertJson([
                'Porcentaje de éxito' => "100%",
                'Sus partidas jugadas' => [
                    [
                        'Partida número' => 1,
                        'dado 1' => 3,
                        'dado 2' => 4,
                        'resultado' => "ganador",
                    ]
                ]
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_cant_see_other_users_games()
    {
        $response = $this->actingAs($this->user, 'api')->getJson('/api/players/' . $this->otherUser->id . '/games');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'No puedes ver las partidas de otros jugadores',
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_play_game()
    {
        $response = $this->actingAs($this->user, 'api')->postJson('/api/players/' . $this->user->id . '/games');

        $response->assertStatus(200);

        $this->game = Game::where('user_id', $this->user->id)->first();
        $this->assertNotNull($this->game);
        $this->assertEquals($this->user->id, $this->game->user_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_cant_play_game_for_other_user()
    {
        $response = $this->actingAs($this->user, 'api')->postJson('/api/players/' . $this->otherUser->id . '/games');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Juega tus propias partidas, no boicotees las estadísticas de los otros jugadores!',
            ]);
    }
    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_delete_their_own_games()
    {

        $response = $this->actingAs($this->user, 'api')->deleteJson('/api/players/' . $this->user->id . '/games');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Se han eliminado todas sus partidas.',
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_cant_delete_other_games()
    {

        $response = $this->actingAs($this->otherUser, 'api')->deleteJson('/api/players/' . $this->user->id . '/games');

        $response->assertStatus(403)
        ->assertJson([
            'message' => 'No puedes eliminar las partidas de otros jugadores',
        ]);
    }
}
