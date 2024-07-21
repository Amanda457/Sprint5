<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

//Rutas abiertas para registarse y hacer login.
Route::post('/players', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/players', [UserController::class, 'showAllPlayers']);
//


Route::middleware(['auth:api'])->group(function () {

    Route::post('/logout', [UserController::class,'logout']);
    Route::put('/players/{id} ', [UserController::class, 'update']); 
    Route::post('/players/{id}/games', [GameController::class, 'playGame']);
    Route::get('/players/{id}/games', [GameController::class, 'showGames']);
    Route::delete('/players/{id}/games', [GameController::class, 'deleteGames']);
    Route::get('/players/ranking', [UserController::class, 'getRanking']);
    Route::get('/players/ranking/winner', [UserController::class, 'getWinner']);
    Route::get('/players/ranking/loser', [UserController::class, 'getLoser']);
    /*
    Route::middleware(['can:is-admin'])->group(function () {
        Route::get('/players', [UserController::class, 'showAllPlayers']);-------OK
        Route::get('/players/ranking', [UserController::class, 'getRanking']);
        Route::get('/players/ranking/loser', [UserController::class, 'getLoser']);
        Route::get('/players/ranking/winner', [UserController::class, 'getWinner']);
    });

    Route::middleware(['can:is-player'])->group(function () {
      
        Route::post('/players/{id}/games', [GameController::class, 'playGame']);---OK
        Route::delete('/players/{id}/games', [GameController::class, 'deleteGames']); ---OK
        Route::get('/players/{id}/games', [GameController::class, 'showGames']);----OK
    });
});*/

});
