<?php

namespace App\Http\Controllers;

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
                'password' => ['required', Password::default()],
            ]
        );

        $nickname = $request->nickname ?? 'Anònim';

        $user = User::create(
            [
                'nickname' => $request->nickname,
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

    /* public function login(Request $request)
    {
        $credentials = [
            'nickname' => $request->nickname,
            'password' => $request->password

        ];

        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('Token')->accesToken;
            return response()->json(['token => $token'], 200);
        } else {
            return response()->json(['error' => 'Credenciales erroneas']);
        }
      
    }

    public function logout(){

      $token = auth()->user()->token();
    
      $token->revoke();

      return response()->json(['success' => 'Logout successfully']);
    }*/

    public function login(Request $request)
    {
        $credentials = $request->only('nickname', 'password'); // More concise

        if (auth()->attempt($credentials)) {
            $user = auth()->user();  // Get authenticated user
            $token = $user->createToken('Token');  // Create token using user instance

            return response()->json([
                'token' => $token->plainTextToken, // Access token in Laravel 11
                'success' => true // Provide more informative response
            ], 200);
        } else {
            return response()->json([
                'error' => 'Credenciales incorrectas', // Use Spanish for consistency
                'success' => false // Indicate failure clearly
            ], 401); // Use appropriate HTTP status code for unauthorized access
        }
    }

    public function logout()
    {
        $user = auth()->user(); // Get authenticated user (if any)

        if ($user) { // Check if user is logged in before revoking token
            $user->tokens()->delete(); // Revoke all tokens for the user (more secure)
        }

        return response()->json([
            'success' => 'Logout successful'
        ], 200);
    }

    public function showAllPlayers()
    {
      //  if (Gate::allows('is-admin')) {
            $users = User::all();

            if ($users->isEmpty()) {
                return response()->json(['message' => 'No hay jugadores registrados'], 200);
            }
            return response()->json($users, 200);
       /* } else {

            abort(403, 'No tienes permisos para realizar esta acción.');
        }*/
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        if ($request->user()->id !== $user->id){

            abort(403, 'No tienes permisos para realizar esta acción.');

        } else{

            $request->validate(['nickname' => ['nullable', 'string', 'max:100']]);
            $user()->nickname = $request->nickname ?? 'Anònim';
            $user->save();
        }
    }
}

