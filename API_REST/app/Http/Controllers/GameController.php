<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;

class GameController extends Controller
{
    
    const WINNED_GAME = 7;
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function playGame()
    {
       
        $game= new Game();
        $game->dice1= rand(1,6);
        $game->dice2= rand(1,6);
        $game->result = $game->dice1 + $game->dice2;
        if ($game->result == WINNED_GAME){
            $game->winner = true;
        } else{
            $game->winner = false;

        }
        $game->store();
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
