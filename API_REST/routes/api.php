<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

//Rutas abiertas para registarse y hacer login.
Route::post('/players', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/players', [UserController::class, 'showAllPlayers']);
//

/*
Route::middleware(['auth:api'])->group(function () {

    Route::post('/logout', [UserController::class,'logout']);
    Route::put('/players/{id} ', [UserController::class, 'update']);--------------OK
    Route::middleware(['can:is-admin'])->group(function () {
        Route::get('/players', [UserController::class, 'showAllPlayers']);-------OK
        Route::get('/players/ranking', [UserController::class, 'getRanking']);
        Route::get('/players/ranking/loser', [UserController::class, 'getLoser']);
        Route::get('/players/ranking/winner', [UserController::class, 'getWinner']);
    });

    Route::middleware(['can:is-player'])->group(function () {
      
        Route::post('/players/{id}/games', [GameController::class, 'play']);
        Route::delete('/players/{id}/games', [GameController::class, 'deleteGames']);
        Route::get('/players/{id}/games', [GameController::class, 'getGames']);
    });
});
*/

?>