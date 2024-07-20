<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Game;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

const WINNED_GAME = 7;

class GameController extends Controller
{

    //const WINNED_GAME = 7;

    /**
     * Display a listing of the resource.
     */
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
                    return response(['message' => 'No tienes partidas.'], 200);
                }
                $games = $user->games()->select('id', 'dice1', 'dice2', 'winner')->get();
                $totalGames = $user->games()->count();
                $wonGames = $user->games()->where('winner', true)->count();
                $winPercentage = ($wonGames / $totalGames) * 100;

                return response()->json(['Porcentaje de éxito' => $winPercentage, 'Sus partidas jugadas' => $games], 200);
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
                $winner = $result == WINNED_GAME;

                if ($winner) {
                    return response()->json(['message' => 'Has ganado!'], 200);
                } else {
                    return response()->json(['message' => 'Loser.'], 200);
                }

                $game = Game::create([
                    'user_id' => $userId,
                    'dice1' => $dice1,
                    'dice2' => $dice2,
                    'winner' => $winner,
                ]);
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
