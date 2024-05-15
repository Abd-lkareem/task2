<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\http\Controllers\AuthController;
use App\models\User; 
use App\Events\UserRegistered;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/verify/{user}', [AuthController::class, 'verify']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum', 'ability:issue-access-token')->group(function () {
    Route::get('/refresh-token', [AuthController::class, 'refreshToken']);
});

Route::middleware("auth:sanctum")->group(function () {
    Route::delete("logout", [AuthController::class, "logout"]);
    Route::get("/test", function(){

        $user = auth()->user();
        // $accessToken = $user->createToken('access_token', ['issue-access-token'], Carbon::now()->addMinutes(config('sanctum.expiration')));
    
        return $user;
    });
    
    
});


