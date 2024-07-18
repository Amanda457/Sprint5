<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Game;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use Illuminate\Http\Request;

const WINNED_GAME = 7;

class GameController extends Controller
{
   
    //const WINNED_GAME = 7;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $games = Game::all();
    }

    /**
     * Show the form for creating a new resource.
     */
   
    public function playGame(Request $request)
    {
        $user = $request->user();
        
        $dice1= rand(1,6);
        $dice2= rand(1,6);
        $result = $dice1 + $dice2;
        $winner = $result == WINNED_GAME;
       

       $game = Game::create([
        'user_id' => $user->id,
        'dice1' => $dice1,
        'dice2' => $dice2,
        'winner' => $winner,
       ]);

       if ($winner){
        return response()->json(['message' => 'Has ganado!'], 200);
    } else{
        return response()->json(['message' => 'Loser.'], 200);
    }
    
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGameRequest $request, Game $game)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        //
    }
}
