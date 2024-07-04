<?php

use App\Http\Controllers\PassportAuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'PassportAuthController@login');
    Route::post('signup', 'PassportAuthController@signUp');
  
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'PassportAuthController@logout');
        Route::get('user', 'PassportAuthController@user');
        /*Para bloquear la lista de jugadores:
        Route::apiResource('players', PlayerController::class);
        y para desloguearte:
        Route:post('logout', [PassportAuthController::clas, 'logout']);
        */
    });
});

Route::post('register', [PassportAuthController::class,'register']);
Route::post('login', [PassportAuthController::class,'login']);
Route::post('logout', [PassportAuthController::class,'logout']);


?>