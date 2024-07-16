<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

//Rutas abiertas para registarse y hacer login.
Route::post('register', [UserController::class,'register']);
Route::post('login', [UserController::class,'login']);
//

/*
Route::middleware(['auth:api'])->group(function () {

    Route::post('/logout', [UserController::class,'logout']);
    Route::middleware(['can:is-admin'])->group(function () {
        Route::get('/admin', [UserController::class, 'index']);
    });

    Route::middleware(['can:is-player'])->group(function () {
        Route::get('/player', [UserController::class, 'index']);
    });
});
*/

?>