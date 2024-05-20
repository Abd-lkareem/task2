<?php

use Illuminate\Support\Facades\Route;
use App\http\Controllers\AuthController;

Route::controller(AuthController::class)
->prefix('users')
->group(function(){

    Route::middleware('auth:sanctum', 'ability:issue-access-token')->group(function () {
        Route::get('/refresh-token',  'refreshToken');
    });
    
    Route::middleware("auth:sanctum")->group(function () {   
        Route::delete("logout", "logout");
    });

    Route::post('/signup', 'signup');
    Route::post('/verify/{user}',  'verify');
    Route::post('/login', 'login')->name('login');
    

    

});



