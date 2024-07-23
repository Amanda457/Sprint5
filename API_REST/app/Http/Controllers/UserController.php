<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate(
            [
                'nickname' => ['nullable', 'string', 'max:100'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => [
                    'required',
                    'string',
                    Password::min(8)
                        ->mixedCase()   // Al menos una letra mayúscula y una minúscula
                        ->numbers()     // Al menos un número
                        ->symbols(),    // Al menos un símbolo
                ],
            ]
        );

        $nickname = $request->nickname ?? 'Anònim';

        $user = User::create(
            [
                'nickname' => $nickname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]
        );

        $token = $user->createToken('Token')->accessToken;

        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken('Token');

            return response()->json([
                'message' => "Sesión iniciada.",
                'token' => $token->accessToken,
            ], 200);
        } else {
            return response()->json([
                'error' => 'Credenciales incorrectas',
                'success' => false
            ], 401);
        }
    }

    public function logout()
    {
        $user = auth()->user();

        if ($user) {
            $user->tokens()->delete();
        }

        return response()->json([
            'success' => 'Adiós, ¡Buena suerte!'
        ], 200);
    }

    public function showAllPlayers()
    {
        if (Gate::allows('is-admin')) {
            $users = User::all();

            if ($users->isEmpty()) {
                return response()->json(['message' => 'No hay jugadores registrados'], 200);
            }
            $users_with_percentage = $this->calculateIndividualWinPercentages($users);
            return response()->json($users_with_percentage, 200);
        } else {

            abort(403, 'No tienes permisos para realizar esta acción.');
        }
    }
    private function calculateIndividualWinPercentages($users)
    {
        return $users->map(function ($user) {
            $games = $user->games()->count();
            $winnedGames = $user->games()->where('winner', true)->count();
            $winPercentage = $games > 0 ? ($winnedGames / $games) * 100 : 0;

            return [
                'ID' => $user->id,
                'Nickname' => $user->nickname,
                'Email' => $user->email,
                'Rol' => $user->rol,
                'Percentatge èxit' => number_format($winPercentage, 2) . "%"
            ];
        });
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        if ($request->user()->id !== $user->id) {

            abort(403, 'No puedes modificar un nickname que no sea el tuyo, que te conozco.');
        } else {

            $request->validate(['nickname' => ['nullable', 'string', 'max:100']]);
            $user->nickname = $request->nickname ?? 'Anònim';
            $user->save();
            return response()->json(['message' => 'Nickname modificado, ¡Buen cambio!.'], 200);
        }
    }

    public function getRanking()
    {
        if (Gate::allows('is-admin')) {
            $users = User::all();

            if ($users->isEmpty()) {
                return response()->json(['message' => 'No hay jugadores registrados'], 200);
            } else {
                $users_with_percentage = $this->calculateIndividualWinPercentages($users);
                $ranking = $users_with_percentage->sortByDesc('Percentatge èxit');
                return response()->json($ranking, 200);
            }
        } else {

            abort(403, 'No tienes permisos para realizar esta acción.');
        }
    }

    public function getWinner()
    {
        if (Gate::allows('is-admin')) {
            $users = User::all();
            if ($users->isEmpty()) {
                return response()->json(['message' => 'No hay jugadores registrados'], 200);
            }
            $users_with_percentage = $this->calculateIndividualWinPercentages($users);
            $winner = $users_with_percentage->sortByDesc('Percentatge èxit')->first();

            return response()->json($winner, 200);
        } else {

            abort(403, 'No tienes permisos para realizar esta acción.');
        }
    }

    public function getLoser()
    {
        if (Gate::allows('is-admin')) {
            $users = User::all();
            if ($users->isEmpty()) {
                return response()->json(['message' => 'No hay jugadores registrados'], 200);
            }
            $users_with_percentage = $this->calculateIndividualWinPercentages($users);
            $winner = $users_with_percentage->sortBy('Percentatge èxit')->first();

            return response()->json($winner, 200);
        } else {

            abort(403, 'No tienes permisos para realizar esta acción.');
        }
    }
}
