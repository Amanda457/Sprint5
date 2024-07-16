<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Http\Requests\StorePlayerRequest;
use App\Http\Requests\UpdatePlayerRequest;
use Illuminate\Support\Facades\Validator;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $players = Player::all();

        if($players->isEmpty()){
            return response()->json(['message' => 'No hay jugadores registrados'], 200);
        }
        return response()->json($players, 200);
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
    public function store(StorePlayerRequest $request)
    {
        $validator= Validator::make($request->all(),[
            'nickname' => 'nullable', 
            'email' =>'required|email',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Player $player)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Player $player)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlayerRequest $request, Player $player)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Player $player)
    {
        //
    }
}
