<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    const WINNED_GAME = 7;

    public function showGames(Request $request, string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        } else {

            $userId = $request->user()->id;

            if ($userId !== $user->id) {
                abort(403, 'No puedes ver las partidas de otros jugadores');
            } else {

                $games = User::find($id)->games;

                if ($games->isEmpty()) {
                    return response(['message' => 'No tienes ninguna partida registrada.'], 200);
                }
                $games = $user->games()->select('id', 'dice1', 'dice2', 'winner')->get();
                $totalGames = $user->games()->count();
                $wonGames = $user->games()->where('winner', true)->count();
                $winPercentage = ($wonGames / $totalGames) * 100;

                $formattedGames = $games->map(function ($game, $index) {
                    return [
                        'Partida número' => $index + 1,
                        'dado 1' => $game->dice1,
                        'dado 2' => $game->dice2,
                        'resultado' => $game->winner ? 'ganador' : 'perdedor'
                    ];
                });
            
                return response()->json([
                    'Porcentaje de éxito' => $winPercentage."%",
                    'Sus partidas jugadas' => $formattedGames
                ], 200);
            }
        }
    }


    public function playGame(Request $request, string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        } else {

            $userId = $request->user()->id;

            if ($userId !== $user->id) {
                abort(403, 'Juega tus propias partidas, no boicotees las estadísticas de los otros jugadores!');
            } else {

                $dice1 = rand(1, 6);
                $dice2 = rand(1, 6);
                $result = $dice1 + $dice2;
                $winner = $result == self::WINNED_GAME;

                $game = Game::create([
                    'user_id' => $userId,
                    'dice1' => $dice1,
                    'dice2' => $dice2,
                    'winner' => $winner,
                ]);

                if ($winner) {
                    return response()->json([
                        'message' => 'Has ganado! Tus dados han tenido estos valores:',
                        'dado 1' => $dice1,
                        'dado 2' => $dice2,
                        'total' => $result], 200);
                } else {
                    return response()->json(['message' => 'Loser. Tus dados han tenido estos lamentables valores:',
                    'dado 1' => $dice1,
                    'dado 2' => $dice2,
                    'total' => $result], 200);
                }

               
            }
        }
    }

    public function deleteGames(Request $request, string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        } else {

            $userId = $request->user()->id;

            if ($userId !== $user->id) {
                abort(403, 'No puedes eliminar las partidas de otros jugadores');
            } else {

                $games = User::find($id)->games;

                if ($games->isEmpty()) {
                    return response(['message' => 'No tienes partidas para eliminar.'], 200);
                }
                $user->games()->delete();
                return response(['message' => 'Se han eliminado todas sus partidas.'], 200);
            }
        }
    }
}
